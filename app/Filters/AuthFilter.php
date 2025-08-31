<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\ResponseTrait;

class AuthFilter implements FilterInterface
{
    use ResponseTrait;

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->has('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        // Optional: Check user role if arguments are provided
        if (!empty($arguments)) {
            $userRole = $session->get('role');
            if (!in_array($userRole, $arguments)) {
                return redirect()->to('/unauthorized');
            }
        }
        
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after the request
        return $response;
    }
}
