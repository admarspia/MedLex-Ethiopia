<?php
class AuthController {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    
   public function register($data) {
    if(empty($data['name']) || empty($data['email']) || empty($data['password'])) 
        return ['status' => 400, 'body' => ['error' => 'All fields required']];
    
    if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) 
        return ['status' => 400, 'body' => ['error' => 'Invalid email']];
    
    // Password minimum length check
    if(strlen($data['password']) < 6) 
        return ['status' => 400, 'body' => ['error' => 'Password must be at least 6 characters']];
    
    return ['status' => 201, 'body' => ['message' => 'Registration successful']];
}
    
    public function login($data) {
        if(empty($data['email']) || empty($data['password'])) 
            return ['status' => 400, 'body' => ['error' => 'Email and password required']];
        
        // Database login check will be written here
        
        return ['status' => 200, 'body' => ['message' => 'Login successful']];
    }
}
?>
