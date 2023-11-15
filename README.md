Table: users

Columns:

id (Primary Key, Auto-increment)
username (Varchar, Unique, Not Null)
password (Varchar, Not Null, Encrypted)
email (Varchar, Unique, Not Null)
created_at (Timestamp)
updated_at (Timestamp)



Table: categories
Columns:

id (Primary Key, Auto-increment)
name (Varchar, Unique, Not Null)


Table: grades
Columns:

id (Primary Key, Auto-increment)
grade (Decimal, Nullable)
user_id (Foreign Key referencing id in the users table, Not Null)
exam_id (Foreign Key referencing id in the exam table, Not Null)
category_id (Foreign Key referencing id in the category table, Not Null)

Table exam:
id (Primary Key, Auto-increment)
name (Varchar, Unique, Not Null)


Relational Model Diagram:

categories (1) --- (N) users
users (1) --- (N) grades
grades (1) --- (1) exam

composer require symfony/security-bundle
created new project
DATABASE_URL=mysql://root:@127.0.0.1:3306/maya?serverVersion=5.7
new database
php bin/console make:user
make userfixtures
created the entitys above

php bin/console doctrine:fixtures:load // carrefull!!! will purge the database
php bin/console doctrine:fixtures:load --append // will add the latest fixture
php bin/console doctrine:fixtures:load --append --group=ExamFixtures 

php bin/console make:auth
make controllers

# config/routes.yaml
home:
    path: /home
    controller: App\Controller\HomeController::index

make the base.html.twig
add css file

Importantnote : when we create a OneToOne relation for exemple grades (1) --- (1) exam  it means that the table can have one exam each the exam must be unique in the table
----------------------------------------------------01
display exams

make pagination https://youtu.be/AcMtHRfg0fk 13:00    https://github.com/KnpLabs/KnpPaginatorBundle  
composer require knplabs/knp-paginator-bundle

the create methode :
php bin/console make:form


ExamType methodes            
controller create methode

form customisation https://symfony.com/doc/current/form/form_customization.html

----------------------------------------------------02
controller:
update methode 
delete methode

templates
----------------------------------------------------03
make sure that the forein keys are displayed correctly
->add('user',EntityType::class,[
                'class' =>User::class,
                'choice_label'=>'username',
             ])

change the relation of the Grade entity from OneToOne to ManyToOne 
use the new methode 
public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
CRUD working
----------------------------------------------------04
add acces denied to routs throu url 
add flash message 
home page
----------------------------------------------------05
secure the admins only routes
unable crud for none admin users

mailer:

composer require symfony/google-mailer 

note: in order to send the message instantally we need to add message_bus: false in mailer.yaml 
framework:
    mailer:
        dsn: '%env(MAILER_DSN)%'
        message_bus: false
then comment MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0   in .env + note for the futur : try the same in order to work with mail providers

created and push the emails in Contact entity 

flash messages
----------------------------------------------------06
new style for the login form
sign up
using a real html css form instead of the ugly RegisterType form
----------------------------------------------------07
to do

add exam link that display the exam +grade+ user 
the navigator password notif
input filters
profile page
acces denied redirection
