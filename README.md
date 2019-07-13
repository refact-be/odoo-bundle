# Odoo bundle

The Symfony bundle ships with two authentication mechanisms you can use to authenticate users from an Odoo instance

## Installation:

    composer require refact-be/odoo-bundle

Add the bundles to bundles.php:

    Refact\OdooBundle\OdooBundle::class => ['all' => true]

Create a configuration file for the Odoo bundle (config/packages/odoo.yaml)

    odoo:
        url: http://localhost:8069
        database: database_name
        admin_id: 2
        admin_pass: admin
        
        # required only for the sso mechanism
        sso_secret: supersecret

        #role_mapping:
        #    1: ROLE_USER_TYPES_INTERNAL_USER
        #    9: ROLE_USER_TYPES_PORTAL

The role_mapping config is optional but allows deep integration of the Odoo Groups and Symfony Roles

Use the odoo:get-roles command to dump the role_mapping automatically:

    bin/console odoo:get-roles

Read data from Odoo:

    /**
     * @Route("/me", name="me_details")
     * @IsGranted("ROLE_USER_TYPES_PORTAL")
     */
    public function users(Odoo $odoo, TokenStorageInterface $tokenStorage)
    {
        $token = $tokenStorage->getToken();

        dump($token);

        dd($odoo->rpc('object', 'execute_kw', ['res.users', 'search_read', [[['login', '=', $token->getUser()]]]])[0]);
    }

## Odoo Form Login

This form extends the traditional Symfony Form Login which allows most of its options to be reused

config/packages/security.yaml

    security:
        firewalls:
            main:
                anonymous: true

                form_login_odoo:
                    #login_path: login

                logout:
                    path: logout

        role_hierarchy:
            # allow internal users to access pages restricted to portal users
            ROLE_USER_TYPES_INTERNAL_USER: ROLE_USER_TYPES_PORTAL

controller:

    /**
     * @Route("/login", name="login")
     * @Route("/login_check", name="login_check")
     * @Route("/logout", name="logout")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        return $this->render('app/login.html.twig', [
            'username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

templates/app/login.html.twig

    {% if error %}
        <p><strong>{{ error.messageKey }}</strong></p>
    {% endif %}

    <form method="post" action="{{ path('login_check') }}">
        <input type="text" name="_username" value="{{ username }}">
        <input type="password" name="_password">
        <button>Log in</button>
    </form>

## Odoo Single Sign-On (SSO)

If users are already logged in in Odoo, it might be a bad user experience to repeat the credentials in the Symfony 
application. SSO allows bridging the two applications to share authentication information. 

This mechanism requires to install and configure the SSO addon in your Odoo instance. 
See https://github.com/refact-be/odoo-sso-addon for additional details

config/packages/security.yaml

    security:
        firewalls:
            main:
                anonymous: true
    
                sso_odoo:
    
                logout:
                    path: logout

config/routes.yaml

    login:
        path: /login
    login_check:
        path: /login_check
    logout:
        path: /logout
