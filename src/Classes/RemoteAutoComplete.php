<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\ErpApi\Facades\ErpApi;
use Amplify\ErpApi\Wrappers\Warehouse;
use Spatie\Url\Url;

// The Easy Ask Session
class RemoteAutoComplete
{
    // connection info
    protected $m_sHostName = null;

    protected $m_nPort = null;

    protected $m_sProtocol = 'http';

    protected $m_sRootUri = 'EasyAsk/AutoComplete-3.0.0.jsp';

    protected $m_options = null;

    public ?Url $url = null;

    /**
     * @return Url|null
     */
    public function getUrl(): ?Url
    {
        return $this->url;
    }

    // Creates the EasyAsk instance.
    //https://steven.prod.easyaskondemand.com/EasyAsk/AutoComplete-3.0.0.jsp?dct=steven.dxp&num=5&key=snap&sort=weight&reduce=cluster&match=true&anchor=true
    public function __construct($host, $port, $dictionary, $protocol)
    {
        $this->m_sHostName = $host;
        $this->m_sProtocol = $protocol;
        $this->m_nPort = $port;
        $this->m_options = new Options($dictionary);
    }

    /**
     */
    public function setDefaultOptions(): void
    {
        $eaDefaultOptions = $this->getOptions()
            ->setResultsPerPage(config('amplify.sayt.suggestion_limit'));

        $this->setOptions($eaDefaultOptions);
    }


    // Creates the generic URL for the website.
    public function formBaseURL()
    {
        $this->url = Url::fromString("/{$this->m_sRootUri}")
            ->withScheme($this->m_sProtocol)
            ->withHost($this->m_sHostName)
            ->withAllowedSchemes(['http', 'https'])
            ->withQueryParameters([
                'dct' => $this->m_options->getDictionary(),
                'num' => $this->m_options->getResultsPerPage(),
                'sort' => 'weight',
                'reduce' => 'cluster',
                'match' => 'true',
                'anchor' => 'true',
//                'site' => parse_url(config('app.url'), PHP_URL_HOST),
            ]);

        if (is_numeric($this->m_nPort)) {
            $this->url = $this->url->withPort($this->m_nPort);
        }

        return $this->url;
    }


    // Sets the EasyAsk options to an Options instance
    public function setOptions($val): void
    {
        $this->m_options = $val;
    }

    // Gets the current EasyAsk Options
    public function getOptions(): Options
    {
        return $this->m_options;
    }

    // User Post does a http POST. Creates a RemoteResults instance and
    //  Posts the URL to get results from the EasyAsk server.
    /**
     * @throws \Exception
     */
    public function urlPost(string $keyword): RemoteSuggestions
    {
        $this->setDefaultOptions();

        $this->url = !empty($url) ? Url::fromString($url) : $this->formBaseURL()->withQueryParameter('key', $keyword);

        $res = new RemoteSuggestions;

        $res->load($this->url);

        return $res;
    }
}
