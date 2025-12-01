<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please log in to continue.');
        }

        if ($arguments) {
            $allowedRoles = is_array($arguments) ? $arguments : explode(',', $arguments);
            $allowedRoles = array_map('trim', $allowedRoles); // Remove whitespace
            $userRole = strtolower(session()->get('role') ?? '');
            
            // Normalize role names (handle both labstaff and lab_staff)
            $normalizedRoles = [];
            foreach ($allowedRoles as $role) {
                $normalizedRoles[] = strtolower(trim($role));
                // Also add underscore variant if it's labstaff
                if (strtolower(trim($role)) === 'labstaff') {
                    $normalizedRoles[] = 'lab_staff';
                }
                // Also add no-underscore variant if it's lab_staff
                if (strtolower(trim($role)) === 'lab_staff') {
                    $normalizedRoles[] = 'labstaff';
                }
            }
            
            if (!in_array($userRole, $normalizedRoles)) {
                return redirect()->to('/login')->with('error', 'Access denied.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}