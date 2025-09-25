<?php

namespace App;

enum Status: string
{
    case WISHLIST = 'wishlist';
    case PLAYING = 'playing';
    case COMPLETED = 'completed';
    case DROPPED = 'dropped';
}