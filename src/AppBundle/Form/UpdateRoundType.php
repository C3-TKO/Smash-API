<?php

namespace AppBundle\Form;

class UpdateRoundType extends RoundType
{
    use UpdatableType;

    public function getName()
    {
        return 'round_edit';
    }
}
