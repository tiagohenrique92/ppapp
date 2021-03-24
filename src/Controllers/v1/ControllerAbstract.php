<?php
namespace PPApp\Controllers\v1;

abstract class ControllerAbstract
{
    const HTTP_STATUS_CODE_200 = 200;
    const HTTP_STATUS_CODE_400 = 400;
    const HTTP_STATUS_CODE_500 = 500;

    /**
     * getPayloadErrorMessage
     *
     * @param integer $code
     * @param string $message
     * @param array $details
     * @return string
     */
    public function getPayloadErrorMessage(int $code, string $message, array $details = null): string
    {
        $data = array(
            "error" => $code,
            "message" => $message,
            "details" => $details,
        );

        return json_encode($data);
    }

    /**
     * getPayloadUnexpectedErrorMessage
     *
     * @param array $details
     * @return string
     */
    public function getPayloadUnexpectedErrorMessage(array $details = null): string
    {
        return $this->getPayloadErrorMessage(1, "Unexpected error", $details);
    }
}
