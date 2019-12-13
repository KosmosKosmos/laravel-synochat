<?php declare(strict_types=1);


namespace KosmosKosmos\SynoChat\Handler;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use KosmosKosmos\SynoChat\Formatter\SynologyChatFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use GuzzleHttp\Client;
use Symfony\Component\Routing\Matcher\UrlMatcher;

/**
 * Sends notifications through Synology Chat API
 *
 * @author Andreas Kosmowicz <andreas@kosmoskosmos.de>
 */
class SynologyChatHandler extends AbstractProcessingHandler {
    /**
     * Synology Chat API token
     *
     * @var string
     */
    private $token;

    private $url;


    public function __construct(
        string $token,
        string $url,
        $level = Logger::ERROR,
        string $version = "2"
    ) {


        $this->url = "https://". $url . "/webapi/entry.cgi?api=SYNO.Chat.External&method=incoming&version=" . $version . "&token=" . $token;
        $this->token = $token;
        parent::__construct($level);

    }

    protected function write(array $record) : void {

        $formData =
            ['form_params' => [
                "payload" => $record['formatted']['payload'],
            ]];

        $client = new Client();

        if (filter_var($this->url, FILTER_VALIDATE_URL)) {
            $response = $client->request("POST", $this->url, $formData);
        } else {
            Log::info("SynoChat enabled but no valid URL given.");
        }

    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter() : FormatterInterface {
        return new SynologyChatFormatter();
    }


}
