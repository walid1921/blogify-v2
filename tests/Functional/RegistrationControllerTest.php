<?php

namespace App\Tests\Functional;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    protected static function getKernelClass (): string
    {
        return Kernel::class;
    }

    public function testRegisterPageLoads (): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        self::assertResponseIsSuccessful();

        // Assert the form exists (this is the real signal)
        self::assertSelectorExists('form[name="registration_form"]');

        // Optional: assert submit button exists
        self::assertSelectorExists('button[type="submit"]');
    }

    public function testUserCanRegister (): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        self::assertSelectorExists('form');

        $form = $crawler->filter('form[name="registration_form"]')->form([
            'registration_form[username]' => 'testuse' . uniqid(),
            'registration_form[email]' => 'func_' . uniqid() . '@test.com',

            // IMPORTANT: plainPassword is a REPEATED field
            'registration_form[plainPassword][first]' => 'Password123!',
            'registration_form[plainPassword][second]' => 'Password123!',

            'registration_form[terms]' => true,
        ]);


        $client->submit($form);

        // Registration redirects
        self::assertResponseRedirects('/');
    }
}
