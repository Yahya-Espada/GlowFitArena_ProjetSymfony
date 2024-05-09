<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\LoginType;
use App\Form\ResetPasswordCodeFormType;
use App\Form\ResetPasswordEmailType;
use App\Form\UserType;
use App\Form\SubcriptionFormType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Http\CurlClient;
use Twilio\Rest\Client;



#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/testsms', name: 'app_user_testsms')]
    public function testSms(): Response
    {
        $TWILIO_SID = "AC0a978fddfd0a9abc68f958eef1b8e6fd";
        $TWILIO_TOKEN = "e4ca3f3830ebf5a032532802d0375e0e";
        $TWILIO_NUMBER = "+13347586498";
        
        $client = new Client($TWILIO_SID, $TWILIO_TOKEN);
       
        $message = "Hello from Symfony";
        
        $client->messages->create(
            "+21621132613",
            [
                'from' => $TWILIO_NUMBER,
                'body' => $message
            ]
        );
        
return $this->redirectToRoute("app_user_login");

    }
    /*$accountSid = getenv('TWILIO_ACCOUNT_SID');
$authToken = getenv('TWILIO_AUTH_TOKEN');
$twilioPhoneNumber = getenv('TWILIO_PHONE_NUMBER');*/

    #[Route('/logout', name: 'app_user_logout')]
    public function logout(SessionInterface $sessionInterface): Response
    {   
        $sessionInterface->set('id_user',"");
        return $this->redirectToRoute("app_user_login");
    }
    #[Route('/resetPassword/request', name: 'app_user_request_resetPassword', methods: ['GET','POST'])]
    public function requestResetPassword(Request $request,MailerInterface $mailer, UserRepository $userRepository,SessionInterface $sessionInterface): Response
    { if($sessionInterface->get('id_user')!=null){
        return $this->redirectToRoute("app_user_login");
        }

        $emailForm = $this->createForm(ResetPasswordEmailType::class);
    $emailForm->handleRequest($request);
    $codeForm=$this->createForm(ResetPasswordCodeFormType::class);
    $codeForm->handleRequest($request);
  
    if($codeForm->isSubmitted()&&$codeForm->isValid()){
        $generatedCode=$sessionInterface->get("codeforReset");
        $codeFormData=$codeForm->getData();
        $codeSubmited=$codeFormData['code'];
        if($codeSubmited==$generatedCode){
            
            return $this->redirectToRoute("app_user_password_validate");

        }else{
            return $this->renderForm('user/resetPassword.html.twig', [
                'codeForm' => $codeForm,
                'error'=>"Code non valide"
            ]);
        }
    }   
     if($emailForm->isSubmitted()&& $emailForm->isValid()){
        $emailFormData=$emailForm->getData();
        $typeOfReset=$request->request->get('resetType');

        $user=null;

        if($typeOfReset!="Email"){
            $phoneNumber=$emailFormData["phoneNumber"];
            $user=$userRepository->findOneBy(["numero_de_telephone"=>$phoneNumber]);

        }else{  
            $email=$emailFormData["email"];
            $user=$userRepository->findOneBy(["email"=>$email]);
        }
            if($user){
                $randomCode=$this->generateRandomNumericString();
                if($typeOfReset!="Email"){
                    //send with phone 
                    $TWILIO_SID = "AC0a978fddfd0a9abc68f958eef1b8e6fd";
                    $TWILIO_TOKEN = "e4ca3f3830ebf5a032532802d0375e0e";
                    $TWILIO_NUMBER = "+13347586498";
                    
                    $client = new Client($TWILIO_SID, $TWILIO_TOKEN);
                   
                    $message = $randomCode." est votre code de reset password";
                    $sessionInterface->set('codeforReset',$randomCode); 
                    $sessionInterface->set('phone_to_reset',$user->getNumeroDeTelephone());

                    $client->messages->create(
                        "+216".$user->getNumeroDeTelephone(),
                        [
                            'from' => $TWILIO_NUMBER,
                            'body' => $message
                        ]
                    );
                }else{
                $createEmail = (new Email())
                ->from('attiayasmine38@gmail.com')
                ->to($email)
                ->subject("GlowFit Arena-ResetPassword")
                ->text($randomCode." est votre code de reset password");
                $mailer->send($createEmail);
                $sessionInterface->set('email_to_reset',$email);
                $sessionInterface->set('codeforReset',$randomCode); 
                }               
                return $this->renderForm('user/resetPassword.html.twig', [
                    'codeForm' => $codeForm,
                    'message'=>"Consulter votre boite mail"
                ]);
                

            }else{
                return $this->renderForm('user/resetPassword.html.twig', [
                    'emailForm' => $emailForm,
                    'error'=>"User n'existe pas "
                ]);
            }
        }
        return $this->renderForm('user/resetPassword.html.twig', [
            'emailForm' => $emailForm,
        ]);

    }
    #[Route('/resetPassword/validate', name: 'app_user_password_validate', methods: ['GET','POST'])]
    public function validateResetPassword(Request $request, UserRepository $userRepository,EntityManagerInterface $entityManage,SessionInterface $sessionInterface): Response
    {
        if(!$sessionInterface->get("codeforReset") || ($sessionInterface->get("email_to_reset")==null && $sessionInterface->get("phone_to_reset")==null)){
            return  $this->redirectToRoute('app_user_request_resetPassword');
        }

        $changePasswordForm = $this->createForm(ChangePasswordType::class);
        $changePasswordForm->handleRequest($request);
        if($changePasswordForm->isSubmitted()&& $changePasswordForm->isValid()){
            $mailUser=$sessionInterface->get("email_to_reset");
            $passwordData=$changePasswordForm->getData();
            $password=$passwordData['motdepasse'];

            $userFromDataBase=$userRepository->findOneBy(['email'=>$mailUser]);
            $hashedPassowrd=password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $userFromDataBase->setMotDePasse($hashedPassowrd);
            $entityManage->flush();
            $sessionInterface->set('email_to_reset',null);
            $sessionInterface->set('codeforReset',null); 
            return $this->redirectToRoute("app_user_login");
        }
           
        return $this->renderForm('user/changePassword.html.twig', [
            'form' => $changePasswordForm,
        ]);
    
        

    }
    #[Route('/admin', name: 'app_user_index')]
    public function index(Request $request ,UserRepository $userRepository,SessionInterface $sessionInterface): Response
    {   
        if(!$this->hasPermissionToRoute($sessionInterface,$userRepository,"admin")){
            return $this->redirectToRoute("app_user_login");
        }
        $roles=$userRepository->findRolesFromDataBase();
        $role=$request->get('role');
        $search=$request->get('search');
        if($search){
            return $this->render('user/index.html.twig', [
                'users' => $userRepository->findBySearch($search),
                'WithconnectedUser'=>true,
                "roles"=>$roles,
                "withFilter"=>true] );  
        }
        else if($role){
            return $this->render('user/index.html.twig', [
                'users' => $userRepository->findBy(['role'=>$role]),
                'WithconnectedUser'=>true,
                "roles"=>$roles,
                "withFilter"=>true            ]);
        }else{
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'WithconnectedUser'=>true,
            "roles"=>$roles
        ]);
    }
    }
    public function hasPermissionToRoute(SessionInterface $sessionInterface,UserRepository $userRepository,string $role){
        $user=$this->getConnectedUser($sessionInterface,$userRepository);
        if($user==null){
            return false;
        }    
        return strtoupper($user->getRole())==strtoupper($role);      
    

    }
    public function getConnectedUser(SessionInterface $sessionInterface,UserRepository $userRepository){
        $id_user=$sessionInterface->get('id_user');
        //var_dump($id_user);
       
        if(isset($id_user)){
           $user=$userRepository->find($id_user);
           return $user;
        }
        return null;

    }
    #[Route('/login', name: 'app_user_login')]
    public function login(Request $request,UserRepository $userRepository,SessionInterface $sessionInterface): Response
    {
        if($sessionInterface->get('id_user')!=null){
            if($this->hasPermissionToRoute($sessionInterface,$userRepository,"admin")){
                //redirect to admin dashboard;
            return    $this->redirectToRoute("app_user_index");
            }else if($this->hasPermissionToRoute($sessionInterface,$userRepository,"user")){
                //redirect to profile user;
            return    $this->redirectToRoute("app_user_profil");
            }else{
                //redirect to coach dashboard 
                return    $this->redirectToRoute("gestion_programme_alimentaire");

            }
        }else{

        $userdataForm = new User();
    $form = $this->createForm(LoginType::class, $userdataForm);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer le mot de passe en clair depuis le formulaire
        $plainPassword = $userdataForm->getMotDePasse();
        $email=$userdataForm->getEmail();
        $userFromDataBase=$userRepository->findOneBy(['email'=>$email]);
        if($userFromDataBase){
            if(password_verify($plainPassword,$userFromDataBase->getMotDePasse())){
                //login cbn 
                $sessionInterface->set('id_user',$userFromDataBase->getId());
                return   $this->redirectToRoute("app_user_login");


            }else{
                $error="Mot de passe incorrect ";
                return $this->renderForm('user/login.html.twig', [
                    'form' => $form,
                    'user'=>$userdataForm,
                    'error'=>$error
                ]);
            }
        }else{
            $error="User n'existe pas ";
            return $this->renderForm('user/login.html.twig', [
                'form' => $form,
                'user'=>$userdataForm,
                'error'=>$error
            ]);
        }
    } return $this->renderForm('user/login.html.twig', [
        'form' => $form,
        'user'=>$userdataForm
    ]);
}
    }
    

    #[Route('/profil', name: 'app_user_profil', methods: ['GET'])]
    public function profil(UserRepository $userRepository,SessionInterface $sessionInterface): Response
    {   
        if(!$this->hasPermissionToRoute($sessionInterface,$userRepository,"user")){
            return $this->redirectToRoute("app_user_login");
        }
        return $this->render('user/profil.html.twig', [
            'user' => $this->getConnectedUser($sessionInterface,$userRepository),
            'WithconnectedUser'=>true

        ]);
    }
    #[Route('/profil_edit', name: 'app_user_edit_profil')]
    public function editProfil(Request $request,  EntityManagerInterface $entityManager,SessionInterface $sessionInterface,UserRepository $userRepository,SluggerInterface $slugger): Response
    {  
        $user=$this->getConnectedUser($sessionInterface,$userRepository);
        $originalImage = $user->getImage();

        $form = $this->createForm(SubcriptionFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                if ($originalImage) {
                    // Delete the old image file
                    $oldFilePath = $this->getParameter('images_directory').'/'.$originalImage;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }

                $user->setImage($newFilename);
            } elseif (!$file && $request->get('remove_image') === 'true') {
                // If checkbox to remove image is checked and no new file is uploaded
                if ($originalImage) {
                    $oldFilePath = $this->getParameter('images_directory').'/'.$originalImage;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                    $user->setImage("");
                }
            }
            $hashed=password_hash($user->getMotDePasse(), PASSWORD_BCRYPT, ['cost' => 12]);
            $user->setMotDePasse($hashed);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_profil', []);
        }

        return $this->renderForm('user/editProfil.html.twig', [
            'user' => $user,
            'form' => $form,
            'WithconnectedUser'=>true

        ]);
    }
    #[Route('/inscription', name: 'app_inscription_new')]
public function inscription(Request $request, EntityManagerInterface $entityManager,SessionInterface $sessionInterface,SluggerInterface $slugger): Response
{ if($sessionInterface->get('id_user')!=null){
    return $this->redirectToRoute("app_user_login");
}
    $user = new User();
    $user->setRole("user");

    $form = $this->createForm(SubcriptionFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $file = $form->get('image')->getData();
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Handle exception if something happens during file upload
            }

            $user->setImage($newFilename);
        }
        // Récupérer le mot de passe en clair depuis le formulaire
        $plainPassword = $user->getMotDePasse();
        // Encoder le mot de passe
        $encodedPassword =password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        // Définir le mot de passe encodé sur l'utilisateur
        $user->setMotDePasse($encodedPassword);
        // Enregistrer l'utilisateur en base de données
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_user_index', []);
    } return $this->renderForm('user/inscription.html.twig', [
        'form' => $form,
    ]);
}

   
 

        #[Route('/new', name: 'app_user_new')]
    public function new(Request $request, EntityManagerInterface $entityManager,SessionInterface $sessionInterface,UserRepository $userRepository,SluggerInterface $slugger): Response
    { if(!$this->hasPermissionToRoute($sessionInterface,$userRepository,"admin")){
        return $this->redirectToRoute("app_user_login");
    }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
    
                    try {
                        $file->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // Handle exception if something happens during file upload
                    }
    
                    $user->setImage($newFilename);
                }
            $plainPassword = $user->getMotDePasse();
            // Encoder le mot de passe
            $encodedPassword =password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 12]);
            // Définir le mot de passe encodé sur l'utilisateur
            $user->setMotDePasse($encodedPassword);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', []);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, UserRepository $userRepository,SessionInterface $sessionInterface): Response
    {
        if(!$this->hasPermissionToRoute($sessionInterface,$userRepository,"admin")){
        return $this->redirectToRoute("app_user_login");
    }
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager,SessionInterface $sessionInterface,UserRepository $userRepository,SluggerInterface $slugger): Response
    {      $originalImage = $user->getImage();
        if(!$this->hasPermissionToRoute($sessionInterface,$userRepository,"admin")){
            return $this->redirectToRoute("app_user_login");
        }
        $form = $this->createForm(SubcriptionFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                if ($originalImage) {
                    // Delete the old image file
                    $oldFilePath = $this->getParameter('images_directory').'/'.$originalImage;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }

                $user->setImage($newFilename);
            } elseif (!$file && $request->get('remove_image') === 'true') {
                // If checkbox to remove image is checked and no new file is uploaded
                if ($originalImage) {
                    $oldFilePath = $this->getParameter('images_directory').'/'.$originalImage;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                    $user->setImage("");
                }
            }
            
            $hashed=password_hash($user->getMotDePasse(), PASSWORD_BCRYPT, ['cost' => 12]);
            $user->setMotDePasse($hashed);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', []);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager,UserRepository $userRepository,SessionInterface $sessionInterface): Response
    {
        if(!$this->hasPermissionToRoute($sessionInterface,$userRepository,"admin")){
            return $this->redirectToRoute("app_user_login");
        }
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', []);
    }

    function generateRandomNumericString($length = 6) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
    
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
    
        return $randomString;
    }
    

    
    #[Route('/search', name: 'app_user_search', methods: ['GET'])]
    public function search(Request $request, UserRepository $userRepository, SessionInterface $sessionInterface): Response
    {
        if (!$this->hasPermissionToRoute($sessionInterface, $userRepository, "admin")) {
            return $this->redirectToRoute("app_user_login");
        }
    
        $searchTerm = $request->query->get('search_term');
    
        if ($searchTerm) {
            $users = $userRepository->findByUsername($searchTerm);
        } else {
            $users = $userRepository->findAll();
        }
    
        return $this->render('user/index.html.twig', [
            'users' => $users,
            'WithconnectedUser' => true
        ]);
    }
    
}
