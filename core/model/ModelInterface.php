<?php

namespace Core\Model;

interface ModelInterface
{
    function delete(): bool;
    function update($data);
    function save();
    function findBy($property, $value);
}
