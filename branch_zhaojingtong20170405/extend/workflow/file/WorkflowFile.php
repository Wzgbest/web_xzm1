<?php
/**
 * User: blu10ph
 */
namespace workflow\file;
abstract class WorkflowFile{
    abstract public function loadFile($file_path);
    abstract public function getWorkFlowItem();
}