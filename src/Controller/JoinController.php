<?php

namespace App\Controller;

use App\Entity\Rooms;
use App\Entity\User;
use App\Form\Type\JoinViewType;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class JoinController extends AbstractController
{
    /**
     * @Route("/join", name="join_index")
     */
    public function index(Request $request, TranslatorInterface $translator, UserPasswordEncoderInterface $encoder)
    {
        $data = array();
        // dataStr wird mit den Daten uid und email encoded übertragen. Diese werden daraufhin als Vorgaben in das Formular eingebaut
        $dataStr = $request->get('data');
        $snack = $request->get('snack');
        $dataAll = base64_decode($dataStr);
        parse_str($dataAll, $data);
        if (isset($data['email']) && isset($data['uid'])) {
            $room = $this->getDoctrine()->getRepository(Rooms::class)->findOneBy(['uid' => $data['uid']]);
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $data['email']]);

            if ($room !== null && $user === $room->getModerator()) {
                return $this->redirectToRoute('room_join', ['room' => $room->getId(), 't' => 'b']);
            }
            $form = $this->createForm(JoinViewType::class, $data);
        } else {
            $snack = 'Data Query konnte nicht gelesen werden. Zugangsdaten manuell eingeben';
            $form = $this->createForm(JoinViewType::class);
        }


        $form->handleRequest($request);
        $errors = array();
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData();
            $room = $this->getDoctrine()->getRepository(Rooms::class)->findOneBy(['uid' => $search['uid']]);
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $search['email']]);

            if (count($errors) == 0 && $room && in_array($user, $room->getUser()->toarray())) {
                $jitsi_server_url = 'https://' . $room->getServer()->getUrl();
                $jitsi_jwt_token_secret = $room->getServer()->getAppSecret();

                $payload = array(
                    "aud" => "jitsi_admin",
                    "iss" => $room->getServer()->getAppId(),
                    "sub" => $room->getServer()->getUrl(),
                    "room" => $room->getUid(),
                    "context" => [
                        'user' => [
                            'name' => $search['name']
                        ]
                    ],
                    "moderator" => false
                );

                $token = JWT::encode($payload, $jitsi_jwt_token_secret);
                $url = $jitsi_server_url . '/' . $room->getUid() . '?jwt=' . $token;
                return $this->redirect($url);
            }
            $snack = $translator->trans('Konferenz nicht gefunden. Zugangsdaten erneut eingeben');
        }

        return $this->render('join/index.html.twig', [
            'form' => $form->createView(),
            'snack' => $snack
        ]);
    }
}
