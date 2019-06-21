<?php


class TradedoublerPixel extends CWidget
{
    private $secretcode;
    private $organization;
    private $event;

    private $isSecure = true;
    private $domain = "tbs.tradedoubler.com";
    private $tduid = '';
    private $reportInfo = '';

    public $orderValue = '';
    public $currency = '';
    public $orderNumber = '';


    public function run() {

        $trackBackUrl = '';

        if ($this->checkInput()) {

            $checkNumberName = "orderNumber";

            $checksum = "v04" . md5($this->secretcode . $this->orderNumber . $this->orderValue);
            if ($this->isSecure)
                $scheme = "https";
            else
                $scheme = "http";

            $trackBackUrl = $scheme . "://" . $this->domain . "/report"
                . "?organization=" . $this->organization
                . "&event=" . $this->event
                . "&" . $checkNumberName . "=" . $this->orderNumber
                . "&checksum=" . $checksum
                . "&tduid=" . $this->tduid
                . "&type=iframe"
                . "&reportInfo=" . $this->reportInfo
                . "&orderValue=" . $this->orderValue
                . "&currency=" . $this->currency;
        }
        $this->render('tradedoubler_pixel', array(
            'url' => $trackBackUrl,
            ));
    }

    private function checkInput () {
        $cfg = include Yii::getPathOfAlias('webroot') . '/cfg/Tradedoubler.php';
        if (empty($cfg)) return false;
        if (!isset($cfg['secretcode']) || !isset($cfg['organization']) || !isset($cfg['event'])) return false;
        else {
            $this->secretcode = $cfg['secretcode'];
            $this->organization = $cfg['organization'];
            $this->event = $cfg['event'];
        }

        if (!empty(Yii::app()->request->cookies['TRADEDOUBLER']->value))
            $this->tduid = Yii::app()->request->cookies['TRADEDOUBLER']->value;
        elseif (!empty(Yii::app()->session['TRADEDOUBLER']))
            $this->tduid = Yii::app()->session['TRADEDOUBLER'];
        else return false;

        if ($this->orderValue == '') return false;
        if ($this->currency == '') return false;
        if ($this->orderNumber == '') return false;

        return true;
    }
}