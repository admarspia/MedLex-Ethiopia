public function searchMedicine($name){
    if(empty(trim($name))){
        return "No medicine provided";
    }

    $name = htmlspecialchars(trim($name));
    return $this->formatResponse($name);
}

private function formatResponse($name){
    return "You searched for: " . $name;
}