<?php

if( $this->witch->craft()->attribute('title') ){
    $title = $this->witch->craft()->attribute('title')->content();
}
if( empty($title) && $this->witch->craft()->attribute('name') ){
    $title = $this->witch->craft()->attribute('name')->content();
}
if( empty($title) ){
    $title = $this->witch->name;
}

$description = false;
if( $this->witch->craft()->attribute('description') ){
    $description = $this->witch->craft()->attribute('description')->content();
}

$image = $this->witch->craft()->attribute('image');

$body = false;
if( $this->witch->craft()->attribute('body') ){
    $body = $this->witch->craft()->attribute('body')->content();
}

$this->view();