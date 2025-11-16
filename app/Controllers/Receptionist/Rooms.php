<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\RoomModel;

class Rooms extends BaseController
{
    protected $roomModel;

    public function __construct()
    {
        $this->roomModel = new RoomModel();
    }

    public function ward($slug)
    {
        $map = [
            'pedia'  => 'Pedia Ward',
            'male'   => 'Male Ward',
            'female' => 'Female Ward',
        ];

        if (!isset($map[$slug])) {
            return redirect()->back()->with('error', 'Unknown ward selected.');
        }

        $wardName = $map[$slug];
        $rooms = $this->roomModel
            ->where('ward', $wardName)
            ->orderBy('room_number', 'ASC')
            ->findAll();

        return view('Reception/rooms/ward', [
            'title' => $wardName . ' Rooms',
            'wardSlug' => $slug,
            'wardName' => $wardName,
            'rooms' => $rooms,
        ]);
    }
}
