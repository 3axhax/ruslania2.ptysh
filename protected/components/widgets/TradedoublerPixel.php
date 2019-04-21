<?php


class TradedoublerPixel extends CWidget
{
    public $secretcode = 'secret';
    public $organization = 'xxxx';
    public $isSecure = true;
    private $domain = "tbs.tradedoubler.com";
    public $event = 'xxxx';
    public $orderValue = '';
    public $currency = '';
    public $orderNumber = '';
    public $tduid = '';
    public $reportInfo = '';

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