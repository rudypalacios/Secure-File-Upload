# Carga segura de archivos con PHP
#### *Con creación de imagen miniatura (thumbnail)
- Una de las formas más comunes de infectar sitios, es con la carga de código malicioso a través de supuestas imágenes que se suben a un sitio web, este código no supone ser infalible, pero sí proporcionar un método de verificación de 6 capas cubriendo:

| Capas de seguridad
| -------------
|Capa 1 - **Verificación de extensión**
| Limitar el tipo de archivos permitidos reduce la probabilidad de carga directa de un archivo con código ejecutable, sin embargo, esta verificación suele ser saltada con facilidad, cambiando la extensión o añadiendo la permitida justo después de la original *(archivo.php.jpg)*
| Capa 2 - **Verificación del tipo de archivo según su encabezado**
| Content-type es un encabezado que se utiliza para identificar los archivos según su naturaleza y formato, es también una buena medida de seguridad cuando se coloca un listado de tipos permitidos, sin embargo, este método tampoco es infalible, pues puede este encabezado puede ser modificado a través de programas especiales o bien cambiando el encabezado de la solicitud, utilizando un *web proxy*
|Capa 3 - **Recrear la imagen**
|Al utilizar una función para recrear una imagen, no copiarla, sacamos del juego el uso de código malicioso insertado en la codificación de un archivo, en el supuesto de que tenga una extensión permitida y un encabezado correcto, aún existe la posibilidad de que la imagen contenga código malicioso dentro de su estructura, es por esto que al recrearla, las funciones suelen retornar un error en la creación al considerarla imagen inválida o bien, la crean con éxito.
| Capa 4 - **Verificar el tamaño de la imagen**
| En este punto del código, esta capa podría estar o faltar, básicamente lo que se hace es verificar que la imagen sea realmente una imagen, es decir, si tiene insertado código malicioso, esta verificación podría retornar valores válidos, pero ya después de *recreada* la imagen, lo más seguro es que sus valores sean válidos y que además la imagen, no tenga el código malicioso o no haya sido creada, es básicamente una doble verificación de imagen creada, válida y correcta.
| Capa 5 - **Cambio de nombre de archivo**
| Este cambio es importante realizarlo, por varias razones, al cambiar el nombre y dejar solamente 1 extensión, se reduce el riesgo de ejecución de archivos con 2 o más extensiones, se reduce la posibilidad de sobreescribir un archivo con el mismo nombre o uno de configuración como un htaccess, ¿por qué hasta este punto y no antes? ... porque en este punto ya sabemos que es de extensión y content-type válido, ya está recreada y tiene los parámetros adecuados, ya vale la pena agregarle este proceso que no es más que el nombre original+el tiempo con milisegundos convertidos a un hash MD5.
| Capa 6 - **Limitación via .htaccess**
| A través del .htaccess, nos aseguramos que las imágenes sean, sí o sí, interpretadas como imágenes, por lo que si logró colarse algún tipo de código aún con las verificaciones anteriores, teóricamente no se ejecutará pues será interpretado como parte de la imagen y no de ningún otro tipo.

Posiblemente la *limitación via .htaccess* pudiera ser la única en implementarse y estar relativamente tranquilos pero nunca está de más tener varias capas de seguridad.

Hay muchas otras técnicas que pueden ser implementadas cuando a carga de archivos se refiere, si quieres saber más sobre las mejores prácticas de seguridad en general o sobre la modificación de encabezado y/o contenido, revisa estos links:
* [Open Web Application Security Project (OWASP) - (esp)](https://translate.googleusercontent.com/translate_c?act=url&depth=1&hl=en&ie=UTF8&prev=_t&rurl=translate.google.com&sl=en&sp=nmt4&tl=es&u=https://www.owasp.org/index.php/Main_Page&usg=ALkJrhj4yoD9e_SdyVVH9M-vACEFzyCe1Q)
* [OWASP - Unrestricted File Upload - (esp)](https://translate.googleusercontent.com/translate_c?act=url&depth=1&hl=en&ie=UTF8&prev=_t&rurl=translate.google.com&sl=en&sp=nmt4&tl=es&u=https://www.owasp.org/index.php/Unrestricted_File_Upload&usg=ALkJrhjsn1jxN2A0A-AkI76ugIBcNf1hEw)
* [Seguridad al cargar imágenes, cómo no hacerlo - (esp)](https://translate.google.com/translate?sl=en&tl=es&js=y&prev=_t&hl=en&ie=UTF-8&u=http%3A%2F%2Fnullcandy.com%2Fphp-image-upload-security-how-not-to-do-it%2F&edit-text=&act=url)
* [JHEAD - (esp)](https://translate.google.com/translate?hl=en&sl=en&tl=es&u=http%3A%2F%2Fwww.sentex.net%2F~mwandel%2Fjhead%2F)

---

# Secure file upload with PHP
#### *With thumbnail creation
- One of the most common ways to infect sites is to load malicious code through supposed images uploaded to a website, this code is not supposed to be infallible, but it does provide a 6-layer verification method covering:

| Security layers
| -------------
| 1st. Layer - **Extension verification**
| Creating a whitelist of extensions reduces the probability of directly loading a file with executable code, however, this validations could be fooled easily by changing the extension or adding the allowed one after the original *(file.php.jpg )*
| 2nd. Layer - **Checking the file type according to its header**
| Content-type is a header used to identify the files according to their nature and format, it is also a good security measure to create a whitelist of content types, however, this method is also not infallible, because this header can be modified through special programs or by changing the header of the request, using a *web proxy*
| 3rd. Layer - **Recreate the image**
| When using a function to recreate an image, not to copy it, we remove from the game the use of malicious code inserted in the file encoding as pure code or as comment, assuming it has an allowed extension and a correct header, there is still the possibility that the image contains malicious code inside, that is why by recreating it, the function usually returns an error if it's an invalid image or it gets successfully created.
| 4th. Layer - **Check image size**
| At this point, this layer could be implemented or not, here we verify that the image is really an image, that's it, if it has malicious code, this verification could return valid values, but after *recreating* the image it's more likely that its values are valid and also that the image is clean, this is basically, a double image verification.
| 5th. Layer - **Change file name**
| This change is important for several reasons, when renaming and leaving only one extension we reduce the risk of running files with 2 or more extensions, also the possibility of overwritting a file with the same name or one of the configuration files, like the htaccess, is reduced so ... why at this point and not before? - because at this point we already know that it's has a valid extension and content-type, also it has been recreated and it has the appropriate parameters and headers, now it's worth the time of adding this process (not that it takes a lot of time either), it is simply *the_original_name+time_with_milliseconds* converted hashed with the MD5 algorithm.
| 6th. Layer - **Limit via .htaccess**
| Through .htaccess we ensure that the images are, one way or another, interpreted as images, so if any malicious code keeps sneaking the security layers, theoretically, will not run, as it will be interpreted as part of the image and won't be executed.

Perhaps if you implement only the *.htaccess* limitation you could be relatively safe, but it never hurts to have multiple layers of security in PHP.

There are many other techniques that can be implemented when uploading files, if you want to know more about best security practices or about the header/content modificationcheck these links:
* [Open Web Application Security Project (OWASP)](https://www.owasp.org/index.php/Main_Page)
* [OWASP - Unrestricted File Upload](https://www.owasp.org/index.php/Unrestricted_File_Upload)
* [PHP Image upload security, how not to do it](http://nullcandy.com/php-image-upload-security-how-not-to-do-it/)
* [JHEAD](Http://www.sentex.net/~mwandel/jhead/)
