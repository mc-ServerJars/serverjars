<?php
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Error\LoaderError;

require_once 'vendor/autoload.php';
require 'db.php';

// Initialize and configure Twig
$loader = new FilesystemLoader('templates/');
$twig = new Environment($loader);

// Page handling
$page = $_GET['p'] ?? 'index';
$startTime = microtime(true);

try {
    $db = Database::getInstance();
    
    // Fetch cache data
    $bucketSize = $db->query("SELECT `value` FROM `cache` WHERE `id` = 1")->get_result()->fetch_assoc()['value'];
    $objectCount = $db->query("SELECT `value` FROM `cache` WHERE `id` = 2")->get_result()->fetch_assoc()['value'];
    
    // Common template data
    $templateData = [
        'renderstart' => $startTime,
        'bucketSize' => $bucketSize,
        'objectCount' => $objectCount,
        'rowCount' => 0,
        'results' => null,
        'fork' => null  // Add fork parameter
    ];

    // Page-specific handling
    switch ($page) {
        case 'search':
            $templateData['render_time'] = microtime(true) - $startTime;
            handleSearchPage($db, $templateData);
            break;
            
        case 'index':
            $templateData['render_time'] = microtime(true) - $startTime;
            handleHomePage($db, $templateData);
            break;
            
        case 'velocity':
        case 'pufferfish':
        case 'arclight':
        case 'sponge':
        case 'leaves':
        case 'mohist':
        case 'neoforge':
        case 'forge':
        case 'quilt':
        case 'bungeecord':
        case 'fabric':
        case 'waterfall':
        case 'folia':
        case 'paper':
        case 'purpur':
        case 'vanilla':
            $templateData['render_time'] = microtime(true) - $startTime;
            $templateData['fork'] = ucfirst($page); // Set fork name
            handleForkPage($db, $page, $templateData);
            break;

        case 'docs':
            $templateData['render_time'] = microtime(true) - $startTime;
            renderTemplate($twig, 'docs.twig', ['title' => 'API Documentation', 'render_time' => $templateData['render_time']]);
            exit;
            
        case '404':
            $templateData['render_time'] = microtime(true) - $startTime;
            renderTemplate($twig, '404.twig', ['title' => '404', 'render_time' => $templateData['render_time']]);
            exit;
            
        default:
            $templateData['render_time'] = microtime(true) - $startTime;
            renderTemplate($twig, '404.twig', ['title' => '404', 'render_time' => $templateData['render_time']]);
            exit;
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $templateData['render_time'] = microtime(true) - $startTime;
    renderTemplate($twig, 'error.twig', [
        'title' => 'Error',
        'message' => 'A database error occurred',
        'render_time' => $templateData['render_time']
    ]);
    exit;
}

/**
 * Handle home page functionality
 */
function handleHomePage($db, &$templateData) {
    try {
        $stmt = $db->query(
            "SELECT * FROM `minecraft_versions`"
        );
        
        $result = $stmt->get_result();
        $templateData['minecraft_versions'] = $result;
        $templateData['rowCount'] = $result->num_rows;
        $templateData['title'] = 'Home';
        
        renderTemplate($GLOBALS['twig'], 'index.twig', $templateData);
    } catch (Exception $e) {
        error_log("Home page error: " . $e->getMessage());
        renderTemplate($GLOBALS['twig'], 'error.twig', [
            'title' => 'Error',
            'message' => "Could not load home page data"
        ]);
    }
}

/**
 * Handle search page functionality
 */
function handleSearchPage($db, &$templateData) {
    if (!isset($_POST['query'], $_POST['type'])) {
        renderTemplate($GLOBALS['twig'], 'search.twig', array_merge(
            $templateData, ['title' => 'Search']
        ));
        return;
    }

    $validColumns = [
        'gv' => 'game_version',
        'sv' => 'software_version',
        'bn' => 'build_number',
        'id' => 'id',
        'fork' => 'fork',
        'name' => 'name'
    ];
    
    $searchType = $_POST['type'];
    $searchColumn = $validColumns[$searchType] ?? 'game_version';
    $searchQuery = $_POST['query'];
    
    try {
        $stmt = $db->query(
            "SELECT * FROM minecraft_versions WHERE $searchColumn = ?",
            [$searchQuery]
        );
        
        $templateData['results'] = $stmt->get_result();
        $templateData['title'] = 'Search Results';
        
        renderTemplate($GLOBALS['twig'], 'search.twig', $templateData);
    } catch (Exception $e) {
        error_log("Search error: " . $e->getMessage());
        renderTemplate($GLOBALS['twig'], 'search.twig', array_merge(
            $templateData, [
                'title' => 'Search',
                'error' => 'Invalid search query'
            ]
        ));
    }
}

/**
 * Handle fork-specific pages
 */
function handleForkPage($db, $fork, &$templateData) {
    try {
        $stmt = $db->query(
            "SELECT * FROM `minecraft_versions` WHERE `fork` = ? ORDER BY `build_number` DESC",
            [$templateData['fork']]
        );
        
        $result = $stmt->get_result();
        $templateData['minecraft_versions'] = $result;
        $templateData['rowCount'] = $result->num_rows;
        
        // Always render fork.twig with the fork parameter
        renderTemplate($GLOBALS['twig'], 'fork.twig', $templateData);
    } catch (Exception $e) {
        error_log("Fork page error ($fork): " . $e->getMessage());
        renderTemplate($GLOBALS['twig'], 'error.twig', [
            'title' => 'Error',
            'message' => "Could not load {$templateData['fork']} data"
        ]);
    }
}

/**
 * Render template with error handling
 */
function renderTemplate($twig, $template, $data) {
    try {
        echo $twig->render($template, $data);
    } catch (LoaderError $e) {
        error_log("Template error ($template): " . $e->getMessage());
        echo $twig->render('error.twig', [
            'title' => 'Error',
            'message' => 'Template not found'
        ]);
    } catch (Exception $e) {
        error_log("Rendering error ($template): " . $e->getMessage());
        echo $twig->render('error.twig', [
            'title' => 'Error',
            'message' => 'Content rendering failed'
        ]);
    }
}