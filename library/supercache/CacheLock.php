<?php

enum CacheLock: int
{
    case LOCK_EX = 2; 
    case LOCK_UN = 3; 
}
