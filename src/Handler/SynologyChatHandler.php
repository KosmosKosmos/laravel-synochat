<?php declare(strict_types=1);


namespace KosmosKosmos\SynoChat\Handler;

use Illuminate\Support\Facades\Log;
use KosmosKosmos\SynoChat\Formatter\SynologyChatFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use GuzzleHttp\Client;

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
        string $version = "2",
        $level = Logger::ERROR
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
        $response = $client->request("POST", $this->url, $formData);


    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter() : FormatterInterface {
        return new SynologyChatFormatter();
    }


}
