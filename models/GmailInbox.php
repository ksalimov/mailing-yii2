<?php
/**
 * Created by PhpStorm.
 * User: Konstantin
 * Date: 2016-12-27
 * Time: 08:06
 */

namespace app\models;

use Yii;
use app\models\Gmail;

class GmailInbox
{
    protected $htmlmsg;
    protected $plainmsg;
//    public $attachments;
    public $mail;

    /*
     * Connect to server via imap
     */
    public function getConnection()
    {
        $hostname = Yii::$app->params['gmailInboxHost'];
        $username = Yii::$app->params['adminEmail'];
        $password = Yii::$app->params['adminEmailPassword'];

        $inbox = imap_open($hostname, $username, $password) or die ('Connot connect to Gmail: ' . imap_last_error());
        return $inbox;
    }

    /*
     * Fetch all mail
     */
    public function fetchMail() {

        $inbox = $this->getConnection();

        $this->mail = array();

        $emails = imap_search($inbox, 'ALL');

        foreach ($emails as $msgno) {
            $gmail = new Gmail();
            $this->fetchHeader($inbox, $msgno, $gmail);
            $this->mail[] = $gmail;
        }

        imap_close($inbox);

        return $this->mail;
    }

    /*
     * Fetch message header
     */
    public function fetchHeader($inbox, $msgno, $gmail)
    {
        $header = imap_header($inbox, $msgno);

        $gmail->id = $header->Msgno;
        $gmail->subject = imap_utf8($header->subject);
        $gmail->date = $header->udate;
        $gmail->from = imap_utf8($header->fromaddress);
    }

    /*
     * Fetch message body
     */
    public function fetchMessage($msgno)
    {
        $inbox = $this->getConnection();

        $this->htmlmsg = $this->plainmsg = '';

        $gmail = new Gmail();

        $this->fetchHeader($inbox, $msgno, $gmail);

        $structure = imap_fetchstructure($inbox, $msgno);
        if(!$structure->parts) {
            $this->getpart($inbox, $msgno, $structure, 0);
        } else {
            foreach ($structure->parts as $partno0 => $p) {
                $this->getpart($inbox, $msgno, $p, $partno0+1);
            }
        }

        if($this->htmlmsg) {
            $gmail->body = $this->htmlmsg;
        } elseif ($this->plainmsg) {
            $gmail->body = $this->plainmsg;
        }

        imap_close($inbox);

        return $gmail;
    }

    /*
     * If body is divided on parts fetch all parts
     */
    public function getpart($mbox,$mid,$p,$partno)
    {
        // $partno = '1', '2', '2.1', '2.1.3', etc for multipart, 0 if simple

        // DECODE DATA
        $data = ($partno)?
            imap_fetchbody($mbox,$mid,$partno):  // multipart
            imap_body($mbox,$mid);  // simple
        // Any part may be encoded, even plain text messages, so check everything.
        if ($p->encoding==4)
            $data = quoted_printable_decode($data);
        elseif ($p->encoding==3)
            $data = base64_decode($data);

        // PARAMETERS
        // get all parameters, like charset, filenames of attachments, etc.
        $params = array();
        if ($p->ifparameters)
            foreach ($p->parameters as $x)
                $params[strtolower($x->attribute)] = $x->value;
        if ($p->ifdparameters)
            foreach ($p->dparameters as $x)
                $params[strtolower($x->attribute)] = $x->value;

        // TEXT
        if ($p->type==0 && $data) {
            // Messages may be split in different parts because of inline attachments,
            // so append parts together with blank row.
            if (strtolower($p->subtype)=='plain')

                $this->plainmsg .= trim($data) . "\n\n";
            else
                $this->htmlmsg.= $data ."<br><br>";
            $charset = $params['charset'];  // assume all parts are same charset
        }

        // EMBEDDED MESSAGE
        // Many bounce notifications embed the original message as type 2,
        // but AOL uses type 1 (multipart), which is not handled here.
        // There are no PHP functions to parse embedded messages,
        // so this just appends the raw source to the main message.
        elseif ($p->type==2 && $data) {
            $this->plainmsg .= $data."\n\n";
        }

        // SUBPART RECURSION
        if (array_key_exists('parts', $p)) {
            foreach ($p->parts as $partno0=>$p2)
                $this->getpart($mbox,$mid,$p2,$partno.'.'.($partno0+1));  // 1.2, 1.2.1, etc.
        }
    }

    public function deleteMessages($ids) {
        $inbox = $this->getConnection();
        foreach ($ids as $msgno) {
            imap_mail_move($inbox, "$msgno:$msgno", '[GMail]/Trash');
            imap_expunge($inbox);
        }
        imap_close($inbox);
    }
}