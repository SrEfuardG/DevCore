<?php
// Inicialización de variables para mensajes de feedback
$success_message = "";
$error_message = "";

// Comprobar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recoger y limpiar los datos del formulario
    $name = trim(htmlspecialchars($_POST['name'] ?? ''));
    $email = trim(htmlspecialchars($_POST['email'] ?? ''));
    $subject = trim(htmlspecialchars($_POST['subject'] ?? ''));
    $message = trim(htmlspecialchars($_POST['message'] ?? ''));

    // 2. Validación de datos
    if (empty($name)) {
        $error_message = "Por favor, ingresa tu nombre.";
    } elseif (empty($email)) {
        $error_message = "Por favor, ingresa tu correo electrónico.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "El formato del correo electrónico no es válido.";
    } elseif (empty($subject)) {
        $error_message = "Por favor, ingresa un asunto.";
    } elseif (empty($message)) {
        $error_message = "Por favor, ingresa tu mensaje.";
    } else {
        // 3. Preparar los detalles del correo electrónico
        $to = "luislego8@gmail.com"; // <-- ¡CAMBIA ESTA LÍNEA CON EL CORREO REAL DE TU EMPRESA!
        $email_subject = "Mensaje de contacto de DevCore Blog: " . $subject;
        $email_body = "Nombre: " . $name . "\n" .
                      "Correo: " . $email . "\n" .
                      "Asunto: " . $subject . "\n\n" .
                      "Mensaje:\n" . $message;
        
        // Encabezados del correo: Usar un remitente del propio dominio para evitar problemas de SPAM en algunos hostings.
        // ¡IMPORTANTE! Reemplaza "tudominio.com" con el dominio real de tu sitio web (ej. devcore.com).
        $headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n"; // Para que puedas responder al cliente original
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // 4. Enviar el correo electrónico
        if (mail($to, $email_subject, $email_body, $headers)) {
            $success_message = "¡Tu mensaje ha sido enviado con éxito! Nos pondremos en contacto contigo pronto.";
            // Opcional: Limpiar los campos del formulario después del envío exitoso
            $name = $email = $subject = $message = "";
        } else {
            $error_message = "Hubo un problema al enviar tu mensaje. Por favor, inténtalo de nuevo más tarde.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevCore - Contacto</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="navbar">
        <div class="logo">
            <a href="index.html">
                <img src="logo.png" alt="Logo DevCore">
            </a>
        </div>
        <nav class="menu">
            <a href="index.html" class="nav-link">Inicio</a>
            <a href="sobre.html" class="nav-link">Sobre DevCore</a>
            <a href="servicios.html" class="nav-link">Servicios</a>
            <a href="portafolio.html" class="nav-link">Portafolio</a>
            <a href="blog.php" class="nav-link">Blog</a>
            <a href="contacto.php" class="nav-link contacto">Contacto</a>
        </nav>
    </header>

    <main class="contact-container">
        <section class="contact-hero">
            <h1>Contáctanos</h1>
            <p>Estamos listos para ayudarte a transformar tus ideas en soluciones de software.</p>
        </section>

        <section class="contact-form-section">
            <h2>Envíanos un Mensaje</h2>

            <?php if (!empty($success_message)): ?>
                <div class="message success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="message error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="contacto.php" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="name">Nombre Completo:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="subject">Asunto:</label>
                    <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="message">Mensaje:</label>
                    <textarea id="message" name="message" rows="6" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn-submit">Enviar Mensaje</button>
            </form>
        </section>

        <section class="contact-info-section">
            <h2>Nuestra Información</h2>
            <div class="info-item">
                <i class="fas fa-map-marker-alt icon-red"></i>
                <p>Tu Dirección, Tu Ciudad, México</p>
            </div>
            <div class="info-item">
                <i class="fas fa-phone icon-red"></i>
                <p>+52 123 456 7890</p>
            </div>
            <div class="info-item">
                <i class="fas fa-envelope icon-red"></i>
                <p>info@devcore.com</p>
            </div>
            <div class="info-item">
                <i class="fas fa-clock icon-red"></i>
                <p>Lunes - Viernes: 9:00 AM - 6:00 PM</p>
            </div>
             <div class="socials-contact-page">
                <h3>Síguenos</h3>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section about">
                <h3>Sobre DevCore</h3>
                <p>DevCore es tu aliado estratégico en el desarrollo de software a medida. Construimos soluciones innovadoras que impulsan tu negocio.</p>
            </div>
            <div class="footer-section links">
                <h3>Enlaces Rápidos</h3>
                <ul>
                    <li><a href="index.html">Inicio</a></li>
                    <li><a href="sobre.html">Sobre DevCore</a></li>
                    <li><a href="servicios.html">Servicios</a></li>
                    <li><a href="portafolio.html">Portafolio</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="contacto.php" class="active">Contacto</a></li>
                </ul>
            </div>
            <div class="footer-section contact">
                <h3>Contacto</h3>
                <p><i class="fas fa-map-marker-alt"></i> Tu Dirección, Tu Ciudad, México</p>
                <p><i class="fas fa-phone"></i> +52 123 456 7890</p>
                <p><i class="fas fa-envelope"></i> info@devcore.com</p>
                <div class="socials">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo date("Y"); ?> DevCore. Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>