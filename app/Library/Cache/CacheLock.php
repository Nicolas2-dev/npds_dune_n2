<?php

namespace App\Library\Cache;


enum CacheLock: int
{
    case LOCK_EX = 2; 
    case LOCK_UN = 3; 
}
