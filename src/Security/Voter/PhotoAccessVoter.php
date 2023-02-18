<?php

namespace App\Security\Voter;

use App\Entity\Galerie;
use App\Entity\Modele;
use App\Entity\Photo;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PhotoAccessVoter extends Voter
{
    public function __construct(private readonly Security $security)
    {
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['view', 'edit'])
            && $subject instanceof Photo;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Modele $user */
        $user = $token->getUser();

        //if ($token instanceof NullToken) {
        //    // the user is not authenticated
        //    return false;
        //}

        /** @var Photo $photo */
        $photo = $subject;
        //dump($photo);

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'edit':
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                break;
            case 'view':
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }

                if ($this->security->isGranted('ROLE_MODELE')
                  && $photo->getShooting()->hasModele($user)) {
                    return true;
                }

                if ($photo->getShooting()->getStatut() == "Public") {
                    return true;
                }

                /** @var Galerie $galerie */
                foreach ($photo->getGaleries() as $galerie) {
                    if ($galerie->getStatut() == "Public"
                     || $galerie->isCover()
                     || $galerie->isFront()
                    ) {
                        return true;
                    }
                }

                break;
        }

        return false;
    }

}
