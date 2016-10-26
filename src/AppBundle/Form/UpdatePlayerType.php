<?php

namespace AppBundle\Form;

class UpdatePlayerType extends PlayerType
{
    use UpdatableType;

    public function getName()
    {
        return 'player_edit';
    }
}
