<?php

namespace Amplify\System\Sayt\Classes\Promo;

use Amplify\System\Sayt\Interfaces\iPromoRemoteEasyAsk;

// The Easy Ask Session
class PromoRemoteEasyAsk implements IPromoRemoteEasyAsk
{
    // connection info
    private $m_sHostName = '';

    private $m_nPort = -1;

    private $m_sProtocol = 'http';

    private $m_sRootUri = 'EasyAsk/apps/CrossSellToResults.jsp';

    private $m_options = null;

    // Creates the EasyAsk instance.
    public function __construct($sHostName, $nPort, $dictionary, $protocol)
    {
        $this->m_sHostName = $sHostName;
        $this->m_nPort = $nPort;
        $this->m_sProtocol = $protocol;
        $this->m_options = new PromoOptions($dictionary);
    }

    // Creates the generic URL for the website.
    private function formBaseURL()
    {
        $port = isset($this->m_nPrt) ? ':'.$this->m_nPort : '';
        //		$port = ($this->m_nPort || sizeof($this->m_nPort) > 0) ? ":" . $this->m_nPort : "";

        return $this->m_sProtocol.'://'.$this->m_sHostName.$port.'/'.$this->m_sRootUri.'?disp=json&oneshot=1&ie=UTF-8';
    }

    // Converts a string parameter to a format usable by the website URL
    private function addParam($name, $val)
    {
        return ($val != null && strlen($val) > 0) ? '&'.$name.'='.$val : '';
    }

    // Coverts a value without a name to a format usable by the website URL
    private function addNonNullVal($val)
    {
        return $val != null ? $val : '';
    }

    // Creates a url for the current host settings and EasyAsk options
    private function formURL()
    {
        return $this->formBaseURL().'&dct='.$this->m_options->getDictionary().'&indexed=1'.
                '&ResultsPerPage='.$this->m_options->getResultsPerPage().
                $this->addParam('eap_GroupID', $this->m_options->getGroupId()).
                $this->addParam('eap_CustomerID', $this->m_options->getCustomerId()).
                $this->addNonNullVal($this->m_options->getCallOutParam());
    }

    // User performs a call to promotions for a particular product and type
    public function getPromotions($productId, $type)
    {
        $url = $this->formURL().'&q='.urlencode($productId).'&type='.urlencode($type);

        return $this->urlPost($url);
    }

    // Sets the protocol.  By default it is http.
    public function setProtocol($protocol)
    {
        $this->m_sProtocol = $protocol;
    }

    // Sets the EasyAsk options to an Options instance
    public function setOptions($val)
    {
        $this->m_options = $val;
    }

    // Gets the current EasyAsk Options
    public function getOptions()
    {
        return $this->m_options;
    }

    // User Post does an http POST. Creates a RemoteResults instance and
    // and Posts the URL to get results from the EasyAsk server.
    public function urlPost($url)
    {
        $res = new PromotionResults;
        $res->load($url);
        echo $url;

        return $res;
    }
}
