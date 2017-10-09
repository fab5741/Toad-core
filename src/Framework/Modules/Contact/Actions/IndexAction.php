<?php

namespace Framework\Modules\Contact\Actions;

use Framework\Actions\RouterAwareAction;
use Framework\Mail\MailInterface;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class IndexAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var MailInterface
     */
    private $mail;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var FlashService
     */
    private $flash;

    use RouterAwareAction;

    /**
     * IndexAction constructor.
     * @param ContainerInterface $container
     * @param RendererInterface $renderer
     * @param MailInterface $mail
     * @param FlashService $flash
     */
    public function __construct(
        ContainerInterface $container,
        RendererInterface $renderer,
        MailInterface $mail,
        FlashService $flash
    ) {
        $this->container = $container;
        $this->renderer = $renderer;
        $this->mail = $mail;
        $this->flash = $flash;
    }

    public function __invoke(Request $request)
    {
        if ($request->getMethod() === "POST") {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->mail->config("localhost", 25, true, true, true, "root", "", "tls");

                $from = $this->container->get("from");
                $this->mail->setFrom($from, "test");
                $this->mail->addAddress($this->container->get("to"), "test");
                $this->mail->subject("Contact : " . $from);

                $body = "Name : " . $request->getParsedBody()['name'] . "\n";
                $body .= "Email : " . $request->getParsedBody()['email'] . "\n";
                $body .= "Message : " . $request->getParsedBody()['message'] . "\n";

                $this->mail->body($body);

                $this->mail->send();
                $this->flash->success("Message has been send, you wil get a reply as soon as possible");
            }
            $errors = $validator->getErrors();
            $name = $request->getParsedBody()['name'];
            $email = $request->getParsedBody()['email'];
            $message = $request->getParsedBody()['message'];
        }
        $module = "contact";
        return $this->renderer->render('@contact/index', compact("module", "name", "email", "message", "errors"));
    }

    protected function getValidator(Request $request)
    {
        $validator = (new Validator($request->getParsedBody()))
            ->required('name', 'email', "message")
            ->length('name', 2, 250)
            ->length('email', 5, 250)
            ->length('message', 20, 250)
            ->email('email');
        return $validator;
    }
}
