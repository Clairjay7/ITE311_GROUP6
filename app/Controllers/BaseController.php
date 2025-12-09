<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
    }

    /**
     * Get nurses who have schedules in nurse_schedules table
     * Only returns nurses with at least one active schedule
     * 
     * @return array
     */
    protected function getNursesWithSchedules()
    {
        $db = \Config\Database::connect();
        
        // Check if nurse_schedules table exists
        if (!$db->tableExists('nurse_schedules')) {
            // If table doesn't exist, return empty array
            return [];
        }
        
        // Get nurses who have at least one schedule
        $nurses = $db->table('users')
            ->select('users.id, users.username, users.email')
            ->join('roles', 'roles.id = users.role_id', 'inner')
            ->join('nurse_schedules', 'nurse_schedules.nurse_id = users.id', 'inner')
            ->where('LOWER(roles.name)', 'nurse')
            ->where('users.status', 'active')
            ->where('users.deleted_at IS NULL', null, false)
            ->where('nurse_schedules.status', 'active')
            ->where('nurse_schedules.shift_date >=', date('Y-m-d')) // Only future or today's schedules
            ->groupBy('users.id') // Group by nurse to avoid duplicates
            ->orderBy('users.username', 'ASC')
            ->get()
            ->getResultArray();
        
        return $nurses;
    }
}
