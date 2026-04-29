<?php

require_once __DIR__ . '/../models/GaleriModel.php';

class GaleriController
{
    private GaleriModel $galeriModel;

    public function __construct(mysqli $db)
    {
        $this->galeriModel = new GaleriModel($db);
    }
}
