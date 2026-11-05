<?php

namespace Router;

use Ctrl\{
    AuthCtrl,
    GtgController,
    HomeCtrl,
    LinkCtrl,
    RecetteCtrl,
    RubricCtrl,
    SnippetCtrl,
    VnCtrl
};
use Ctrl\Admin\{
    CatLangCtrl as AdmCatLngCtrl,
    CatCtrl as AdmCatCtrl,
    ContactCtrl as AdmContCtrl,
    LanguageCtrl as AdmLngCtrl,
    LinkCtrl as AdmLnkCtrl,
    TypRubCtrl as AdmTypRubCtrl,
    TypeCtrl as AdmTypCtrl,
    RubricCtrl as AdmRubCtrl,
    SnippetCtrl as AdmSnipCtrl,
    UserCtrl as AdmUsrCtrl,
    RecetteCtrl as AdmRecCtrl
};
use Exception\PourNotNumericException;
use Form\{
    AdmSearchForm,
    RecetteForm,
    SearchForm
};
use Core\{
    Html\Form,
    Util\ErrorManager
};
use PDO;

/**
 * C'est le routeur du site
 * @package Router
 */
class Router
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var PDO
     */
    private $db;

    /**
     * @var array
     */
    private $segments = [];

    /**
     * Router constructor.
     * @param array $params
     * @param PDO $db
     */
    public function __construct(array $params, PDO $db)
    {
        $this->params = $params;
        $this->db = $db;

        // On récupère le chemin de l'URL (ex: /rubric/5 -> ["rubric", "5"])
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = trim($requestUri, '/');
        $this->segments = $path !== '' ? explode('/', $path) : [];
    }

    public function route(): void
    {
        // Le premier segment de l'URL devient la cible (par défaut 'rubric')
        $target = $this->segments[0] ?? 'rubric';

        switch ($target) {
            case 'rubric':
                // On récupère l'ID soit dans l'URL (/rubric/5), soit en paramètre de secours
                $id = $this->segments[1] ?? ($this->params['id'] ?? null);
                $this->rubric($id);
                break;
            case 'autres_sites':
                $this->otherSites();
                break;
            case 'link':
                $this->link();
                break;
            case 'vietnam':
                $this->vietnam();
                break;
            case 'galerie':
                $this->galerie();
                break;
            case 'recette':
                $this->recette();
                break;
            case 'contact':
                $this->contact();
                break;
            case 'snippet':
                $this->snippet();
                break;
            case 'auth':
                $this->auth();
                break;
            case 'admin':
                $this->admin();
                break;
            default:
                $this->notFound();
        }
    }

    private function rubric($id = null): void
    {
        $ctrl = new RubricCtrl($this->db);
        if ($id !== null) { // Si un id de rubrique est présent alors on l'affiche
            $ctrl->show($id);
        } else { // Sinon on affiche la page d'accueil
            $ctrl->index();
        }
    }

    private function otherSites(): void
    {
        (new HomeCtrl())->otherSites();
    }

    private function vietnam(): void
    {
        (new VnCtrl($this->db))->home();
    }

    private function galerie(): void
    {
        (new VnCtrl($this->db))->galerie();
    }

    private function contact(): void
    {
        $form = new Form($_POST);
        (new HomeCtrl())->contact($form);
    }


    private function notFound(): void
    {
        (new GtgController())->notFound();
    }

    private function recette(): void
    {
        $id = $this->segments[1] ?? ($this->params['id'] ?? null);

        if ($id !== null) { // Si un id de recette est présent alors on l'affiche
            $ctrl = new RecetteCtrl($this->db);
            $ctrl->show($id);
        } else { // Sinon on affiche la page vietnam
            $ctrl = new VnCtrl($this->db);
            $ctrl->home();
        }
    }

    private function link(): void
    {
        $action = $this->segments[1] ?? ($this->params['action'] ?? null);
        $id = $this->segments[2] ?? ($this->params['id'] ?? null);

        if ($action === 'open' && $id !== null) {
            $ctrl = new LinkCtrl($this->db);
            $ctrl->open($id);
        } else {
            $this->notFound();
        }
    }

    private function snippet(): void
    {
        $action = $this->segments[1] ?? ($this->params['action'] ?? null);

        if ($action === 'search') {
            $this->snipSearch();
        } else {
            // Si le segment 1 est un nombre, on le considère comme un ID
            $id = $this->segments[1] ?? ($this->params['id'] ?? null);

            $ctrl = new SnippetCtrl($this->db);
            if ($id !== null && is_numeric($id)) {
                $ctrl->one($id, new SearchForm($this->params));
            } else {
                $ctrl->all();
            }
        }
    }

    private function snippets(): void
    {
        $ctrl = new SnippetCtrl($this->db);
        if (isset($this->params['id'])) {
            $ctrl->one($this->params['id'], new SearchForm($this->params));
        } else {
            $ctrl->all();
        }
    }

    private function snipSearch(): void
    {
        $ctrl = new SnippetCtrl($this->db);
        if (!empty($this->params)) {
            $form = new SearchForm($this->params);
            $ctrl->search($form);
        } else {
            $ctrl->all();
        }
    }

    private function auth(): void
    {
        $action = $this->segments[1] ?? ($this->params['action'] ?? null);

        switch ($action) {
            case 'subscribe':
                $this->subscribe();
                break;
            case 'loginForm':
                $this->loginForm();
                break;
            default:
                (new RubricCtrl($this->db))->index();
        }
    }

    public function subscribe()
    {
        (new AuthCtrl($this->db))->subscribe();
    }

    public function loginForm()
    {
        (new AuthCtrl($this->db))->loginForm();
    }

    private function admin(): void
    {
        // On extrait la cible admin du segment 1 (ex: /admin/link -> 'link')
        $admTarg = $this->segments[1] ?? ($this->params['admTarg'] ?? 'link');

        if (!isset($this->params['action']) && isset($this->segments[2])) {
            $this->params['action'] = $this->segments[2];
        }
        if (!isset($this->params['id']) && isset($this->segments[3])) {
            $this->params['id'] = $this->segments[3];
        }

        switch ($admTarg) {
            case 'contact':
                $this->adminContact();
                break;
            case 'link':
                $this->handleCrud(AdmLnkCtrl::class, 'all');
                break;
            case 'typAndRub':
                $this->typsAndRubs();
                break;
            case 'type':
                $this->handleCrud(AdmTypCtrl::class, 'all');
                break;
            case 'rubric':
                $this->handleCrud(AdmRubCtrl::class, 'all');
                break;
            case 'user':
                $this->handleCrud(AdmUsrCtrl::class, 'all', 'users');
                break;
            case 'recette':
                $this->adminRecette();
                break;
            case 'snippet':
                $this->adminSnippet();
                break;
            case 'catAndLang':
                $this->catsAndLangs();
                break;
            case 'cat':
                $this->handleCrud(AdmCatCtrl::class, 'all');
                break;
            case 'language':
                $this->handleCrud(AdmLngCtrl::class, 'all');
                break;
            default:
                $this->handleCrud(AdmLnkCtrl::class, 'all');
        }
    }

    /**
     * Méthode générique pour gérer tous les CRUD standards d'administration
     */
    private function handleCrud(string $ctrlClass, string $defaultMethod, ?string $customDefaultMethod = null): void
    {
        $ctrl = new $ctrlClass($this->db);
        $action = $this->params['action'] ?? null;
        $id = $this->params['id'] ?? null;

        switch ($action) {
            case 'insert':
                $ctrl->ajouter(new Form($_POST));
                break;

            case 'update':
                if ($id !== null && is_numeric($id)) {
                    $ctrl->modifier($id, new Form($_POST));
                } else {
                    $ctrl->notFound();
                }
                break;

            case 'delete':
                if ($id !== null) {
                    $ctrl->supprimer($id, new Form($_POST));
                } else {
                    $ctrl->notFound();
                }
                break;

            default:
                if ($id !== null && method_exists($ctrl, 'one')) {
                    $ctrl->one($id, new SearchForm($this->params));
                } else {
                    if ($customDefaultMethod && method_exists($this, $customDefaultMethod)) {
                        $this->$customDefaultMethod();
                    } else {
                        $ctrl->$defaultMethod();
                    }
                }
        }
    }

    private function adminContact(): void
    {
        $ctrl = new AdmContCtrl($this->db);
        $action = $this->params['action'] ?? null;
        $id = $this->params['id'] ?? null;

        switch ($action) {
            case 'reply':
                if ($id !== null) {
                    $ctrl->repondre($id);
                } else {
                    $ctrl->notFound();
                }
                break;
            case 'sendReply':
                $ctrl->envRep(new Form($_POST));
                break;
            case 'insert':
                $ctrl->ajouter(new Form($_POST));
                break;
            case 'delete':
                if ($id !== null) {
                    $ctrl->supprimer($id, new Form($_POST));
                } else {
                    $ctrl->notFound();
                }
                break;
            default:
                $ctrl->all();
        }
    }

    private function adminRecette(): void
    {
        $ctrl = new AdmRecCtrl($this->db);
        $action = $this->params['action'] ?? null;
        $id = $this->params['id'] ?? null;

        switch ($action) {
            case 'insert':
                try {
                    $ctrl->ajouter(new RecetteForm($_POST));
                } catch (PourNotNumericException $e) {
                    ErrorManager::add('Le champ \'pour\' doit contenir une valeur numérique !');
                    $form = new Form(array_diff_key($_POST, ['pour' => '']));
                    $ctrl->modifierAvantAjouter($form);
                }
                break;

            case 'update':
                if ($id !== null && is_numeric($id)) {
                    try {
                        $ctrl->modifier($id, new RecetteForm($_POST));
                    } catch (PourNotNumericException $e) {
                        ErrorManager::add('Le champ \'pour\' doit contenir une valeur numérique !');
                        $form = new Form(array_diff_key($_POST, ['pour' => '']));
                        // Correction d'une faute de frappe présente dans votre code ("modifierAvantAjunter") :
                        $ctrl->modifierAvantAjouter($form);
                    }
                } else {
                    $ctrl->notFound();
                }
                break;

            case 'delete':
                if ($id !== null) {
                    $ctrl->supprimer($id, new Form($_POST));
                } else {
                    $ctrl->notFound();
                }
                break;

            default:
                $ctrl->all();
        }
    }

    private function adminSnippet(): void
    {
        $ctrl = new AdmSnipCtrl($this->db);
        $action = $this->params['action'] ?? null;
        $id = $this->params['id'] ?? null;

        switch ($action) {
            case 'search':
                if (!empty($this->params)) {
                    $ctrl->search(new AdmSearchForm($this->params));
                } else {
                    $ctrl->all();
                }
                break;
            case 'insert':
                $ctrl->ajouter(new Form($_POST));
                break;
            case 'update':
                if ($id !== null && is_numeric($id)) {
                    $ctrl->modifier($id, new Form($_POST));
                } else {
                    $ctrl->notFound();
                }
                break;
            case 'delete':
                if ($id !== null) {
                    $ctrl->supprimer($id, new Form($_POST));
                } else {
                    $ctrl->notFound();
                }
                break;
            default:
                if ($id !== null) {
                    $ctrl->one($id, new AdmSearchForm($this->params));
                } else {
                    $ctrl->all();
                }
        }
    }

    private function typsAndRubs(): void
    {
        (new AdmTypRubCtrl($this->db))->all();
    }

    private function catsAndLangs(): void
    {
        (new AdmCatLngCtrl($this->db))->all();
    }

    private function users(): void
    {
        (new AdmUsrCtrl($this->db))->all();
    }
}
