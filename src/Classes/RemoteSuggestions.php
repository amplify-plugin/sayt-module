<?php

namespace Amplify\System\Sayt\Classes;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use IteratorAggregate;
use JsonSerializable;
use Traversable;
use function Adminer\first;

class RemoteSuggestions implements JsonSerializable, IteratorAggregate
{
    private $m_doc;

    private string $m_keyword = '';

    private array $m_suggests = [];

    public function __construct()
    {
        $this->m_doc = new \DOMDocument;
    }

    // Loads a URL into the instance, then determines the appropriate results and layout.

    /**
     * @throws \Exception
     */
    public function load($url): void
    {
        try {

            $response = Http::timeout(30)
                ->asForm()
                ->withoutVerifying()
                ->acceptJson()
                ->withQueryParameters($url->getAllQueryParameters())
                ->get((string)$url->withoutQueryParameters());

            $responseContent = $response->body();

            $responseContent = (!empty($responseContent))
                ? trim($responseContent)
                : '{}';

            $this->m_doc = json_decode(
                json_validate($responseContent) ? $responseContent : "[$responseContent]",
                false,
                512,
                JSON_THROW_ON_ERROR);

        } catch (\GuzzleHttp\Exception\ConnectException|\Illuminate\Http\Client\ConnectionException $connectException) {
            abort(500, "Unable to connect to EasyAsk server on {$url->withoutQueryParameters()}.");
        } catch (\Exception $exception) {
            Log::info($exception);
            abort(500, $exception->getMessage());
        }
    }

    public function getQuestion()
    {
        $this->m_keyword = $this->m_doc->inputs ?? '';

        return $this->m_keyword;
    }

    private function processSuggestions()
    {
        foreach ($this->m_doc?->suggests ?? [] as $suggestion) {
            $this->m_suggests[] = new Suggestion($suggestion);
        }
    }

    /**
     * @return Suggestion[]
     */
    public function getSuggestions(): array
    {
        if (empty($this->m_suggests)) {
            $this->processSuggestions();
        }

        return $this->m_suggests;
    }

    public function getFirstSuggestion(): Suggestion
    {
        if (empty($this->m_suggests)) {
            $this->processSuggestions();
        }

        return first($this->m_suggests);
    }

    public function hasSuggestion(): bool
    {
        if (empty($this->m_suggests)) {
            $this->processSuggestions();
        }

        return !empty($this->m_suggests);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4
     */
    public function jsonSerialize(): mixed
    {
        return $this->m_doc;
    }

    /**
     * Retrieve an external iterator
     *
     * An instance of an object implementing Iterator or Traversable
     *
     * @return Traversable<TKey, TValue>|TValue[]
     *
     * @throws Exception on failure.
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->getSuggestions());
    }
}