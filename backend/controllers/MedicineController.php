public function searchMedicine($name){
    if($name == null){
        return "No medicine provided";
    }

    return $this->formatResponse($name);
}

private function formatResponse($name){
    return "You searched for: " . $name;
}