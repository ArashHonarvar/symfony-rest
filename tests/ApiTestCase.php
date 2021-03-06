<?php
declare(strict_types=1);

namespace App\Tests;

use App\Entity\Programmer;
use App\Entity\User;
use App\Tests\ResponseAsserter;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;
use \GuzzleHttp\Client;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Tests\Encoder\PasswordEncoder;

class ApiTestCase extends KernelTestCase
{

    private static $staticClient;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var FormatterHelper
     */
    private $formatterHelper;


    private $output;

    private $responseAsserter;


    public static function setUpBeforeClass(): void
    {
        $baseUri = getenv("TEST_BASE_URI");
        self::$staticClient = new Client([
            "base_uri" => $baseUri,
            "defaults" => [
                "exceptions" => false
            ],
            "enviroment" => "test"
        ]);

        self::bootKernel();
    }

    public function setUp(): void
    {
        $this->client = self::$staticClient;
        $this->purgeDatabase();
    }


    protected function onNotSuccessfulTest(\Throwable $e): void
    {
        throw $e;
    }

    protected function tearDown(): void
    {
        //Overriding
    }

    private function purgeDatabase()
    {
        $purger = new ORMPurger($this->getService('doctrine')->getManager());
        $purger->purge();
    }

    private function getService($id)
    {
        return self::$kernel->getContainer()->get($id);
    }

    protected function debugResponse(ResponseInterface $response)
    {
        $body = (string)$response->getBody();
        $contentType = $response->getHeader('Content-Type')[0];
        if ($contentType == 'application/json' || strpos($contentType, '+json') !== false) {
            $data = json_decode($body);
            if ($data === null) {
                // invalid JSON!
                $this->printDebug($body);
            } else {
                // valid JSON, print it pretty
                $this->printDebug(json_encode($data, JSON_PRETTY_PRINT));
            }
        } else {
            $isValidHtml = strpos($body, '</body>') !== false;
            if ($isValidHtml) {
                $this->printDebug('');
                $crawler = new Crawler($body);

                // very specific to Symfony's error page
                $isError = $crawler->filter('#traces-0')->count() > 0
                    || strpos($body, 'looks like something went wrong') !== false;
                if ($isError) {
                    $this->printDebug('There was an Error!!!!');
                    $this->printDebug('');
                } else {
                    $this->printDebug('HTML Summary (h1 and h2):');
                }

                // finds the h1 and h2 tags and prints them only
                foreach ($crawler->filter('h1, h2')->extract(array('_text')) as $header) {
                    // avoid these meaningless headers
                    if (strpos($header, 'Stack Trace') !== false) {
                        continue;
                    }
                    if (strpos($header, 'Logs') !== false) {
                        continue;
                    }

                    // remove line breaks so the message looks nice
                    $header = str_replace("\n", ' ', trim($header));
                    // trim any excess whitespace "foo   bar" => "foo bar"
                    $header = preg_replace('/(\s)+/', ' ', $header);

                    if ($isError) {
                        $this->printErrorBlock($header);
                    } else {
                        $this->printDebug($header);
                    }
                }

                $profilerUrl = $response->getHeader('X-Debug-Token-Link') ? $response->getHeader('X-Debug-Token-Link')[0] : null;
                if ($profilerUrl) {
                    $fullProfilerUrl = $response->getHeader('Host')[0] . $profilerUrl;
                    $this->printDebug('');
                    $this->printDebug(sprintf(
                        'Profiler URL: <comment>%s</comment>',
                        $fullProfilerUrl
                    ));
                }

                // an extra line for spacing
                $this->printDebug('');
            } else {
                $this->printDebug($body);
            }
        }
    }

    /**
     * Print a message out - useful for debugging
     *
     * @param $string
     */
    private function printDebug($string)
    {
        if ($this->output === null) {
            $this->output = new ConsoleOutput();
        }

        $this->output->writeln($string);
    }

    /**
     * Print a debugging message out in a big red block
     *
     * @param $string
     */
    protected function printErrorBlock($string)
    {
        if ($this->formatterHelper === null) {
            $this->formatterHelper = new FormatterHelper();
        }
        $output = $this->formatterHelper->formatBlock($string, 'bg=red;fg=white', true);

        $this->printDebug($output);
    }


    protected function createUser($username, $plainPassword = "123")
    {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail("arash.honarvar.1372@gmail.com");
        $password = $this->getService('security.password_encoder')->encodePassword($user, $plainPassword);
        $user->setPassword($password);
        $this->getEm()->persist($user);
        $this->getEm()->flush();
        return $user;
    }

    protected function createProgrammer(array $data)
    {
        $data = array_merge([
            'powerLevel' => rand(0, 10),
            'user' => $this->getEm()->getRepository(User::class)->findAny()
        ], $data);

        $accessor = PropertyAccess::createPropertyAccessor();
        $programmer = new Programmer();
        foreach ($data as $key => $value) {
            $accessor->setValue($programmer, $key, $value);
        }

        $this->getEm()->persist($programmer);
        $this->getEm()->flush();

        return $programmer;

    }

    /**
     * @return EntityManager
     */
    protected function getEm()
    {
        return $this->getService('doctrine.orm.entity_manager');
    }

    protected function asserter()
    {
        if ($this->responseAsserter === null) {
            $this->responseAsserter = new ResponseAsserter();
        }
        return $this->responseAsserter;
    }
}
