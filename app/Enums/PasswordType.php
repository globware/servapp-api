<?php

namespace app\Enums;

enum PasswordType: string
    {
        case USER = 'user';
        case CLIENT = 'client';
    }