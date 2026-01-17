@extends('emails.layout')
    @section('title')
        New Registration
    @endsection

    @section('heading')
        New Account Registration
    @endsection
    
    @section('content')
        
        <div style="padding: 20px;">
            <h2>Dear {{ $user->name }} We are pleased to welcome you to ServeApp</h2>
            
            <p>Use the Code below to verify your email.</p>
            
            <div style="background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;">
                {{ $code }}
            </div>
        </div>
        
    @endsection

