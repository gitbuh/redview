RedView
==========================================================

RedView is a view management framework for PHP.

>   **Alpha notice!**
>
>   RedView is still very alpha. Everything is subject to change.
>   Things should generally work, but use it at your own risk.
>   If you want a stable version, fork [RedView on GitHub] :)

----------------------------------------------------------
Highlights
----------------------------------------------------------

-   Provides an intuitive and unintrusive foundation for your web application's
    front end.

-   Facilitates a modular, object-oriented programming style.

-   Routes page requests and form posts automatically.

-   Create [custom tags] for use in your [markup] with a few lines of code.

-   Extensible by plugins via events.

-   Integrates seamlessly with (but doesn't require) [RedBean] and [RedModel].

----------------------------------------------------------
Design Notes
----------------------------------------------------------

RedView tries to achieve the same elegant design and straightforward
usability as RedBean.

Many classes make up the framework, but most of the functionality is 
accessible via a facade class (like RedBean's *R*) named RedView.

The [controller] base class is designed to be subclassed many times in an
[application], and much of the application's interaction with the RedView
framework will occur within the context of a [view's][view] controller. 

To make your life easier, the RedView facade extends the base controller. 
This means your controllers can simply extend RedView and most of the
framework will be accessible through *self*.

----------------------------------------------------------
Definitions
----------------------------------------------------------


The following sections define some terminology used in documenting and
discussing RedView.


<p id="Action"></p>

-   ### Action

    An action is something that happens as a result of a form post. 

    Create a normal XHTML form in your [markup] with the *action* attribute 
    set to the name of a public non-static method of the [controller] class
    and RedView will handle the rest.


<p id="Application"></p>

-   ### Application

    Your application is part or all of the web site or web application you're
    working on. For an example application, see [RedSkeleton].


<p id="Cache"></p>

-   ### Cache

    RedView will parse your [markup], applying any [custom tags], and write the
    resulting PHP script to a filesystem cache.


<p id="Controller"></p>

-   ### Controller

    A PHP class providing functionality for a [view]. Your view controllers
    should extend the *RedView* class. 
    

<p id="Custom_Tag"></p>

-   ### Custom Tag

    A custom tag is a PHP class representing an extension to HTML, provided
    either by the framework or by your [application].

    Custom tags may alter [markup] before it is written to the [cache]. Tags
    have full access to the entire node structure of the markup, and can alter
    it in any way, including by creating [processing instructions].

    Custom tags may override existing XHTML elements or add new elements. 
    For example, by default RedView will append a processing instruction to a
    form's children via a custom "form" tag.

>   **Hint**
>
>   Custom tags should not modify the markup in a way that 
>   reflects the state of the current session or request.
>
>   Although this may work during testing, things will break 
>   once caching is enabled. 
>
>   Instead, create a processing instruction or two, possibly 
>   calling a static method of your custom tag. If you need 
>   to access the tag's contents at runtime, use output buffering.

<p id="Markup"></p>

-   ### Markup

    A well-formed XHTML fragment which may include [custom tags] and
    [processing instructions].

    Any PHP produced by custom tags or contained in processing instructions
    will execute within the scope of a public method of an instance of the
    [controller] class for the document containing the markup.

    In other words, *$this* will refer to an instance of the controller for
    this [view], and *self* will refer to the controller class itself.
 
>   **Hint**
>
>   Sending HTML to the browser after receiving an HTTP post instead of
>   redirecting causes usability issues with the browser's navigation controls.

<p id="Processing_Instruction"></p>

-   ### Processing Instruction

    A PHP processing instruction, for example:

        <?php
          $user = $_SESSION['user'];
          echo $user->name;
        ?>

>   **Hint**
>
>   Processing instructions should not appear in HTML tag attributes in your
>   [markup].
>
>   The markup is parsed as XML before it is parsed as PHP, so the outcome is
>   at the mercy of PHP's XML writer (which will dutifully recode your intended
>   PHP code as XML entities). 
>
>   Create a [custom tag] or store the string content of the opening tag in a
>   [slot] instead of putting processing instructions inside of tag attributes.


<p id="View"></p>

-   ### View

    A view is a part of your application. A view is composed of one or both of:

    -   a file named *viewname*.php containing a [controller].

    -   a file named *viewname*.html containing some [markup].


<p id="View_State"></p>

-   ### View State

    A [view] can be in the following states during a request: 


    -   #### Action

        An [action] requested by an HTTP post is being performed.  
        This state occurs when a certain *$_REQUEST* key is present.

    -   #### Redirect

        The browser is being redirected to another (on-site) URL.  
        The *Action* state is always followed by the *Redirect* state.

    -   #### Load

        The PHP script generated from your [markup] is running.  
        This state occurs if no action was requested.

----------------------------------------------------------
See Also
----------------------------------------------------------


-   [RedBean], an object persistence framework (my fork).

-   [RedModel], model definitions for RedBean.

-   [RedSkeleton], an example app built on RedBean, RedModel, and RedView.

----------------------------------------------------------

>   *More documentation to come...*



[RedView on GitHub]:https://github.com/gitbuh/redview
[RedBean]:https://github.com/gitbuh/redbean
[RedModel]:https://github.com/gitbuh/redmodel
[RedSkeleton]:https://github.com/gitbuh/redskeleton

[Action]:#Action
[Actions]:#Action
[Application]:#Application
[Controller]:#Controller
[Custom Tag]:#Custom_Tag
[Slot]:#Slot
[Markup]:#Markup
[Cache]:#Cache
[View]:#View
[Processing Instruction]:#Processing_Instruction

[Custom Tags]:#Custom_Tag
[Caching]:#Caching
[Processing Instructions]:#Processing_Instruction
