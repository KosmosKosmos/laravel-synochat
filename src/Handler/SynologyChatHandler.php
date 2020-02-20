<?php declare(strict_types=1);


namespace KosmosKosmos\SynoChat\Handler;

use Illuminate\Support\Facades\Cache;
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
        $level = Logger::ERROR,
        string $version = "2"
    ) {


        $this->url = "https://". $url . "/webapi/entry.cgi?api=SYNO.Chat.External&method=incoming&version=" . $version . "&token=" . $token;
        $this->token = $token;
        parent::__construct($level);

    }

    protected function write(array $record) : void {

        if (Cache::has("synochat_chat_handler_paused_since")) {
            Log::info("Won't connect to SynoChat API now, since we will risk to be blocked. Maybe we are already blocked. Message to be sent:\n".$record['formatted']['message']);
            return;
        }

        $formData =
            ['form_params' => [
                "payload" => $record['formatted']['payload'],
            ]];

        $client = new Client();

        if (filter_var($this->url, FILTER_VALIDATE_URL)) {
            $clientResponse = $client->request("POST", $this->url, $formData);

            if ($clientResponse->getStatusCode() !== 200) {
                Log::info("Error ".$clientResponse->getStatusCode()." connecting to SynoChat API via ".$this->url.". Seems that the DiskStation is not available here.");
            }

            $responseBody = json_decode((string) $clientResponse->getBody());

            if (!$responseBody->success) {

                if (!is_iterable($responseBody->error->errors)) {
                    Log::info("Error in SynoChat: ".$responseBody->error->code);
                } else  {
                   Log::info("Error ".$responseBody->error->code." connecting to SynoChat API : ".$responseBody->error->errors);
                    if (in_array($responseBody->error->code, [404, 105])) {
                        Cache::put(
                            "synochat_chat_handler_paused_since",
                            date("Y-m-d H:i:s"),
                            config("synochat.time_to_wait_before_retry_after_failed",60));
                    }
                }
            }

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
