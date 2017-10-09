<?php

namespace Framework\Actions;

use Framework\Database\Table;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Stdlib\ResponseInterface;

class CrudAction
{
    /**
     * @var string
     */
    protected $viewPath;
    /**
     * @var string
     */
    protected $routePrefix;
    /**
     * @var string
     */
    protected $messages = [
        'create' => "L'élément à bien été crée",
        "edit" => "L'élément à bien été édité",
        "delete" => "L'élément à bien été supprimé",
    ];
    /**
     * @var Table
     */
    protected $table;
    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var FlashService
     */
    private $flash;


    /**
     * PostCrudActions constructor.
     * @param RendererInterface $renderer
     * @param Router $router
     * @param Table $table
     * @param FlashService $flash
     * @internal param SessionInterface $session
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        Table $table,
        FlashService $flash
    ) {

        $this->renderer = $renderer;
        $this->router = $router;
        $this->table = $table;
        $this->flash = $flash;
    }

    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);
        if ($request->getMethod() === "DELETE") {
            return $this->delete($request);
        }
        if (substr((string)$request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute("id")) {
            return $this->edit($request);
        } else {
            return $this->index($request);
        }
    }

    /**
     * @param Request $request
     * @return \Psr\Http\Message\ResponseInterface|ResponseInterface
     */
    public function delete(Request $request)
    {
        $this->table->delete($request->getAttribute("id"));
        $this->flash->success($this->messages["delete"]);
        return $this->redirect('blog.admin.index');
    }

    /**
     * @param Request $request
     * @return \Psr\Http\Message\ResponseInterface|string
     */
    public function create(Request $request)
    {
        $item = $this->getNewEntity();

        if ($request->getMethod() === "POST") {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->insert($this->getParams($request, $item));
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $item = $request->getParsedBody();
            $errors = $validator->getErrors();
        }
        return $this->renderer->render($this->viewPath . '/create', $this->formParams(compact('item', 'errors')));
    }

    protected function getNewEntity()
    {
        return [];
    }

    protected function getValidator(Request $request)
    {
        var_dump($request->getUploadedFiles());
        return new Validator(array_merge($request->getParsedBody(), $request->getUploadedFiles()));
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getParams(Request $request, $item = null): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content', "created_at"]);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param array|null $params
     * @return array
     */
    protected function formParams(?array $params): array
    {
        return $params;
    }

    /**
     * Edit an item
     *
     * @param Request $request
     * @return ResponseInterface|string
     *
     */
    public function edit(Request $request)
    {
        $item = $this->table->find($request->getAttribute("id"));

        if ($request->getMethod() === "POST") {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->update($item->id, $this->getParams($request, $item));
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $errors = $validator->getErrors();
            $params = $request->getParsedBody();
            $params['id'] = $item->id;
            $item = $params;
        }
        return $this->renderer->render($this->viewPath . '/edit', $this->formParams(compact('item', 'errors')));
    }

    /**
     * get items
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->table->findPaginated(12, $params['p'] ?? 1);

        return $this->renderer->render($this->viewPath . '/index', compact('items'));
    }
}
