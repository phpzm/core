<?php

namespace Simples\Core\Message;

use ForceUTF8\Encoding;
use Simples\Core\Helper\File;
use Simples\Core\Helper\Json;
use Simples\Core\Helper\Text;

/**
 * Class Sender
 * @package Apocalipse\Message\Message
 */
class Mail
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $toAddress;

    /**
     * @var string
     */
    protected $toName;

    /**
     * @var string
     */
    protected $alt;

    /**
     * @var string
     */
    protected $fromAddress;

    /**
     * @var string
     */
    protected $fromName;

    /**
     * @var string
     */
    protected $replyToAddress;

    /**
     * @var string
     */
    protected $replyToName;

    /**
     * @var array
     */
    protected $attachments = [];

    /**
     * @var array
     */
    protected $ccs = [];

    /**
     * @var string
     */
    private $error;

    /**
     * @var string
     */
    const STATUS_WAITING = 'waiting';

    /**
     * @var string
     */
    const STATUS_SENT = 'sent';

    /**
     * @var string
     */
    const STATUS_ERROR = 'error';

    /**
     * EMail constructor.
     * @param string $subject
     * @param string $message
     * @param string $toAddress
     * @param string $toName
     * @param string $alt
     * @param string $fromAddress
     * @param string $fromName
     */
    public function __construct($subject, $message, $toAddress, $toName = '', $alt = '', $fromAddress = '', $fromName = '')
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->toAddress = $toAddress;
        $this->toName = $toName;
        $this->alt = $alt;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
        $this->id = uniqid();
    }

    /**
     * @param string $driver
     * @return bool
     */
    public function send($driver = 'default')
    {
        $sent = false;

        if ($this->toAddress) {
            $file = $this->id . '.' . 'mail';

            $root = path(true, 'storage/files/mail');

            $waiting = path($root, self::STATUS_WAITING, $file);
            if (File::exists($waiting)) {
                File::destroy($waiting);
            }

            $settings = off(config('mail'), $driver);

            $mailer = new \PHPMailer();

            $mailer->isSMTP();
            $mailer->SMTPAuth = true;

            $mailer->Host = off($settings, 'host');
            $mailer->Port = off($settings, 'port');
            $mailer->SMTPSecure = off($settings, 'secure');
            $mailer->Username = off($settings, 'user');
            $mailer->Password = off($settings, 'password');

            $mailer->addAddress($this->toAddress, $this->toName ? $this->toName : '');

            if (!$this->fromAddress) {
                if (!($this->fromAddress = off($settings, 'address'))) {
                    $this->fromAddress = $mailer->Username;
                }
            }
            if (!$this->fromName) {
                $this->fromName = off($settings, 'name', off(config('app'), 'name'));
            }

            $mailer->setFrom($this->fromAddress, $this->fromName);

            if ($this->replyToAddress) {
                $mailer->addReplyTo($this->replyToAddress, of($this->replyToName, ''));
            }

            foreach ($this->ccs as $cc) {
                $mailer->addCC($cc->address, $cc->name);
            }

            $mailer->isHTML(true);

            $mailer->Subject = Encoding::fixUTF8($this->subject);
            $mailer->Body = Encoding::fixUTF8(Text::replace($this->message, '{id}', $this->id));
            $mailer->AltBody = $this->alt;

            foreach ($this->attachments as $attachment) {
                $mailer->addAttachment($attachment->filename, $attachment->description);
            }

            $filename = path($root, self::STATUS_SENT, $file);
            $sent = $mailer->send();

            if (!$sent) {
                $filename = path($root, self::STATUS_ERROR, $file);
                $this->error = $mailer->ErrorInfo;
            }

            File::write($filename, $this->json());
        }

        return $sent;
    }

    /**
     * @return int|null
     */
    public function schedule()
    {
        $filename = path(true, 'storage/files/mail', self::STATUS_WAITING, $this->id . '.' . 'mail');
        if (!File::exists($filename)) {
            if (File::write($filename, $this->json())) {
                return $this->id;
            }
        }
        return null;
    }

    /**
     * @param $id
     * @param string $status
     * @return mixed
     */
    public function load($id, $status = null)
    {
        $status = of($status, self::STATUS_WAITING);

        $filename = path(true, 'storage', 'files', 'mail', $status, $id . '.' . 'mail');

        if (File::exists($filename)) {
            $properties = Json::decode(File::read($filename));
            foreach ($properties as $key => $value) {
                /** @noinspection PhpVariableVariableInspection */
                $this->$key = $value;
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function json()
    {
        $properties = [];
        foreach ($this as $key => $value) {
            $properties[$key] = $value;
        }

        return Json::encode($properties);
    }

    /**
     * @param $address
     * @param string $name
     */
    public function addCC($address, $name = '')
    {
        $this->ccs[] = (object)['address' => $address, 'name' => $name];
    }

    /**
     * @param $filename
     * @param string $description
     * @return bool
     */
    public function addAttachment($filename, $description = '')
    {
        if (File::exists($filename)) {
            $this->attachments[] = (object)['filename' => $filename, 'description' => $description];
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getToAddress()
    {
        return $this->toAddress;
    }

    /**
     * @param string $toAddress
     */
    public function setToAddress($toAddress)
    {
        $this->toAddress = $toAddress;
    }

    /**
     * @return string
     */
    public function getToName()
    {
        return $this->toName;
    }

    /**
     * @param string $toName
     */
    public function setToName($toName)
    {
        $this->toName = $toName;
    }

    /**
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    /**
     * @return string
     */
    public function getFromAddress()
    {
        return $this->fromAddress;
    }

    /**
     * @param string $fromAddress
     */
    public function setFromAddress($fromAddress)
    {
        $this->fromAddress = $fromAddress;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @param string $fromName
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
    }

    /**
     * @return string
     */
    public function getReplyToAddress()
    {
        return $this->replyToAddress;
    }

    /**
     * @param string $replyToAddress
     */
    public function setReplyToAddress($replyToAddress)
    {
        $this->replyToAddress = $replyToAddress;
    }

    /**
     * @return string
     */
    public function getReplyToName()
    {
        return $this->replyToName;
    }

    /**
     * @param string $replyToName
     */
    public function setReplyToName($replyToName)
    {
        $this->replyToName = $replyToName;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param array $attachments
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;
    }

    /**
     * @return array
     */
    public function getCcs()
    {
        return $this->ccs;
    }

    /**
     * @param array $ccs
     */
    public function setCcs($ccs)
    {
        $this->ccs = $ccs;
    }
}
