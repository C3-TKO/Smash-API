<?php

namespace AppBundle\Form;

class UpdateGameType extends GameType
{
    use UpdatableType;

    public function getName()
    {
        return 'game_edit';
    }
}
