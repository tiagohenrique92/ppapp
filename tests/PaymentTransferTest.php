<?php
namespace PPApp;

use Faker\Factory;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use PPApp\Client\Http;
use PPApp\Models\TransactionModel;
use PPApp\Models\UserModel;
use PPApp\Models\UserModelFactory;
use PPApp\Models\WalletModel;
use PPApp\Models\WalletModelFactory;

final class PaymentTransferTest extends TestCase
{
    /**
     * @var Client
     */
    private $http;

    /**
     * @var Factory
     */
    private $faker;

    /**
     * @var UserModelFactory
     */
    private $userFactory;

    /**
     * @var WalletModelFactory
     */
    private $walletFactory;

    protected function setUp(): void
    {
        require_once __DIR__ . "/../app/config.php";
        require_once __DIR__ . "/../app/bootstrap.php";
        $this->faker = Factory::create();
        $this->userFactory = new UserModelFactory();
        $this->walletFactory = new WalletModelFactory();
        $this->http = new Client(array(
            "verify" => false,
            "base_uri" => "https://myppapp.test/api/v1/payment",
            "http_errors" => false,
        ));
    }

    /**
     * createFakeBusinessUser
     *
     * @return UserModel
     */
    protected function createFakeBusinessUser(): UserModel
    {
        $userModel = $this->userFactory->makeBusinessUser();
        $userModel->save();
        return $userModel;
    }

    /**
     * createFakePersonUser
     *
     * @return UserModel
     */
    protected function createFakePersonUser(): UserModel
    {
        $userModel = $this->userFactory->makePersonUser();
        $userModel->save();
        return $userModel;
    }

    /**
     * createFakeWallet
     *
     * @param integer $id_user
     * @param float $balance
     * @return WalletModel
     */
    protected function createFakeWallet(int $id_user, float $balance): WalletModel
    {
        $walletModel = $this->walletFactory->make($id_user, $balance);
        $walletModel->save();
        return $walletModel;
    }

    /**
     * @testdox Pagamento com sucesso - de usuario comum para usuario comum
     * @return void
     */
    public function testPagamentoComSucessoDeUsuarioComumParaUsuarioComum(): void
    {
        $payerUser = $this->createFakePersonUser();
        $payerWallet = $this->createFakeWallet($payerUser->id, 1000);

        $payeeUser = $this->createFakePersonUser();
        $payeeWallet = $this->createFakeWallet($payeeUser->id, 1000);

        $payload = array(
            "payerUuid" => $payerUser->uuid,
            "payeeUuid" => $payeeUser->uuid,
            "amount" => 50,
        );

        $response = $this->http->post(null, array(
            "json" => $payload,
        ));

        $jsonResponse = json_decode($response->getBody(), true);

        $this->assertEquals(Http::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayHasKey("uuid", $jsonResponse);
        $this->assertNotEmpty($jsonResponse['uuid']);

        $transactionModel = new TransactionModel();
        $transactionModel::where("uuid", $jsonResponse['uuid']);
        $transactionModel->delete();
        $payerWallet->delete();
        $payeeWallet->delete();
        $payerUser->delete();
        $payeeUser->delete();
    }

    /**
     * @testdox Pagamento com sucesso - de usuario comum para usuario empresarial
     * @return void
     */
    public function testPagamentoComSucessoDeUsuarioComumParaUsuarioEmpresarial(): void
    {
        $payerUser = $this->createFakePersonUser();
        $payerWallet = $this->createFakeWallet($payerUser->id, 1000);

        $payeeUser = $this->createFakeBusinessUser();
        $payeeWallet = $this->createFakeWallet($payeeUser->id, 1000);

        $payload = array(
            "payerUuid" => $payerUser->uuid,
            "payeeUuid" => $payeeUser->uuid,
            "amount" => 50,
        );

        $response = $this->http->post(null, array(
            "json" => $payload,
        ));

        $jsonResponse = json_decode($response->getBody(), true);

        $this->assertEquals(Http::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayHasKey("uuid", $jsonResponse);
        $this->assertNotEmpty($jsonResponse['uuid']);

        $transactionModel = new TransactionModel();
        $transactionModel::where("uuid", $jsonResponse['uuid']);
        $transactionModel->delete();
        $payerWallet->delete();
        $payeeWallet->delete();
        $payerUser->delete();
        $payeeUser->delete();
    }

    /**
     * @testdox Pagamento sem sucesso - de usuario comum para ele mesmo
     * @return void
     */
    public function testPagamentoSemSucessoDeUsuarioComumParaEleMesmo(): void
    {
        $payerUser = $this->createFakePersonUser();
        $payerWallet = $this->createFakeWallet($payerUser->id, 1000);

        $payload = array(
            "payerUuid" => $payerUser->uuid,
            "payeeUuid" => $payerUser->uuid,
            "amount" => 50,
        );

        $response = $this->http->post(null, array(
            "json" => $payload,
        ));

        $jsonResponse = json_decode($response->getBody(), true);

        $this->assertEquals(Http::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayHasKey("error", $jsonResponse);
        $this->assertEquals($jsonResponse['error'], 6);

        $payerWallet->delete();
        $payerUser->delete();
    }

    /**
     * @testdox Pagamento sem sucesso - de usuario empresarial para usuario empresarial
     * @return void
     */
    public function testPagamentoSemSucessoDeUsuarioEmpresarialParaUsuarioEmpresarial(): void
    {
        $payerUser = $this->createFakeBusinessUser();
        $payerWallet = $this->createFakeWallet($payerUser->id, 1000);

        $payeeUser = $this->createFakeBusinessUser();
        $payeeWallet = $this->createFakeWallet($payeeUser->id, 1000);

        $payload = array(
            "payerUuid" => $payerUser->uuid,
            "payeeUuid" => $payeeUser->uuid,
            "amount" => 50,
        );

        $response = $this->http->post(null, array(
            "json" => $payload,
        ));

        $jsonResponse = json_decode($response->getBody(), true);

        $this->assertEquals(Http::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayHasKey("error", $jsonResponse);
        $this->assertEquals($jsonResponse['error'], 5);

        $payerWallet->delete();
        $payerUser->delete();
        $payeeWallet->delete();
        $payeeUser->delete();
    }

    /**
     * @testdox Pagamento sem sucesso - de usuario empresarial para usuario comum
     * @return void
     */
    public function testPagamentoSemSucessoDeUsuarioEmpresarialParaUsuarioComum(): void
    {
        $payerUser = $this->createFakeBusinessUser();
        $payerWallet = $this->createFakeWallet($payerUser->id, 1000);

        $payeeUser = $this->createFakePersonUser();
        $payeeWallet = $this->createFakeWallet($payeeUser->id, 1000);

        $payload = array(
            "payerUuid" => $payerUser->uuid,
            "payeeUuid" => $payeeUser->uuid,
            "amount" => 50,
        );

        $response = $this->http->post(null, array(
            "json" => $payload,
        ));

        $jsonResponse = json_decode($response->getBody(), true);

        $this->assertEquals(Http::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayHasKey("error", $jsonResponse);
        $this->assertEquals($jsonResponse['error'], 5);

        $payerWallet->delete();
        $payerUser->delete();
        $payeeWallet->delete();
        $payeeUser->delete();
    }

    /**
     * @testdox Pagamento sem sucesso - de usuario empresarial para ele mesmo
     * @return void
     */
    public function testPagamentoSemSucessoDeUsuarioEmpresarialParaEleMesmo(): void
    {
        $payerUser = $this->createFakeBusinessUser();
        $payerWallet = $this->createFakeWallet($payerUser->id, 1000);

        $payload = array(
            "payerUuid" => $payerUser->uuid,
            "payeeUuid" => $payerUser->uuid,
            "amount" => 50,
        );

        $response = $this->http->post(null, array(
            "json" => $payload,
        ));

        $jsonResponse = json_decode($response->getBody(), true);

        $this->assertEquals(Http::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayHasKey("error", $jsonResponse);
        $this->assertEquals($jsonResponse['error'], 5);

        $payerWallet->delete();
        $payerUser->delete();
    }

    /**
     * @testdox Pagamento sem sucesso - de usuario com saldo insulficiente
     * @return void
     */
    public function testPagamentoSemSucessoDeUsuarioComSaldoInsulficiente(): void
    {
        $payerUser = $this->createFakePersonUser();
        $payerWallet = $this->createFakeWallet($payerUser->id, 0);

        $payeeUser = $this->createFakePersonUser();
        $payeeWallet = $this->createFakeWallet($payeeUser->id, 1000);

        $payload = array(
            "payerUuid" => $payerUser->uuid,
            "payeeUuid" => $payeeUser->uuid,
            "amount" => 50,
        );

        $response = $this->http->post(null, array(
            "json" => $payload,
        ));

        $jsonResponse = json_decode($response->getBody(), true);

        $this->assertEquals(Http::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayHasKey("error", $jsonResponse);
        $this->assertEquals($jsonResponse['error'], 9);

        $payerWallet->delete();
        $payerUser->delete();
        $payeeWallet->delete();
        $payeeUser->delete();
    }

    /**
     * @testdox Pagamento sem sucesso - valor de pagamento menor ou igual a zero
     * @return void
     */
    public function testPagamentoSemSucessoValorDePagamentoMenorOuIgualAZero(): void
    {
        $payerUser = $this->createFakePersonUser();
        $payerWallet = $this->createFakeWallet($payerUser->id, 1000);

        $payeeUser = $this->createFakePersonUser();
        $payeeWallet = $this->createFakeWallet($payeeUser->id, 1000);

        $payload = array(
            "payerUuid" => $payerUser->uuid,
            "payeeUuid" => $payeeUser->uuid,
            "amount" => -10,
        );

        $response = $this->http->post(null, array(
            "json" => $payload,
        ));

        $jsonResponse = json_decode($response->getBody(), true);

        $this->assertEquals(Http::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayHasKey("error", $jsonResponse);
        $this->assertEquals($jsonResponse['error'], 10);

        $payerWallet->delete();
        $payerUser->delete();
        $payeeWallet->delete();
        $payeeUser->delete();
    }
}
