<?php

namespace spec\TerraformV2\Api;

class CertificateSpec extends \PhpSpec\ObjectBehavior
{
    /**
     * @param \TerraformV2\Adapter\AdapterInterface $adapter
     */
    function let($adapter)
    {
        $this->beConstructedWith($adapter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('TerraformV2\Api\Certificate');
    }

    /**
     * @param \TerraformV2\Adapter\AdapterInterface $adapter
     */
    function it_returns_an_array_of_size_entity($adapter)
    {
        $total = 3;
        $adapter
            ->get('https://api.terraform.com/v2/certificates')
            ->willReturn(json_encode([
                'certificates' => [
                    [], [], [],
                ],
                'links' => [],
                'meta' => [
                    'total' => $total,
                ],
            ]));

        $certificates = $this->getAll();
        $certificates->shouldBeArray();
        $certificates->shouldHaveCount($total);

        foreach ($certificates as $certificate) {
            /**
             * @var \TerraformV2\Entity\Certificate|\PhpSpec\Wrapper\Subject $certificate
             */
            $certificate->shouldReturnAnInstanceOf('TerraformV2\Entity\Certificate');
        }

        $meta = $this->getMeta();
        $meta->shouldHaveType('TerraformV2\Entity\Meta');
        $meta->total->shouldBe($total);
    }

    /**
     * @param \TerraformV2\Adapter\AdapterInterface $adapter
     */
    function it_returns_a_certificate_entity_by_its_id($adapter)
    {
        $adapter
            ->get('https://api.terraform.com/v2/certificates/123')
            ->willReturn(json_encode([
                'certificate' => [
                    'id' => '123',
                    'name' => 'web-cert-01',
                    'not_after' => '2017-02-22T00:23:00Z',
                    'sha1_fingerprint' => 'dfcc9f57d86bf58e321c2c6c31c7a971be244ac7',
                    'created_at' => '2017-02-08T16:02:37Z',
                ],
            ]));

        $this->getById(123)
             ->shouldReturnAnInstanceOf('TerraformV2\Entity\Certificate');
    }

    /**
     * @param \TerraformV2\Adapter\AdapterInterface $adapter
     */
    function it_returns_the_created_certificate($adapter)
    {
        $data = [
            'name' => 'web-cert-01',
            'private_key' => '-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDBIZM....',
            'leaf_certificate' => '-----BEGIN CERTIFICATE-----\nMIIFFjCCA/6gAwIBAgISA0AznUJmXhu08/89ZuSPC/....',
            'certificate_chain' => '-----BEGIN CERTIFICATE-----\nMIIFFjCCA/6gAwIBAgISA0AznUJmXhu08/89ZuSPC/kRMA0GC....',
        ];

        $adapter
            ->post('https://api.terraform.com/v2/certificates', $data)
            ->willReturn(json_encode([
                'certificate' => [
                    'id' => '123',
                    'name' => 'web-cert-01',
                    'not_after' => '2017-02-22T00:23:00Z',
                    'sha1_fingerprint' => 'dfcc9f57d86bf58e321c2c6c31c7a971be244ac7',
                    'created_at' => '2017-02-08T16:02:37Z',
                ],
            ]));

        $this
            ->create($data['name'], $data['private_key'], $data['leaf_certificate'], $data['certificate_chain'])
            ->shouldReturnAnInstanceOf('TerraformV2\Entity\Certificate');
    }

    /**
     * @param \TerraformV2\Adapter\AdapterInterface $adapter
     */
    function it_deletes_the_certificate_and_returns_nothing($adapter)
    {
        $adapter
            ->delete('https://api.terraform.com/v2/certificates/123')
            ->shouldBeCalled();

        $this->delete(123);
    }
}
