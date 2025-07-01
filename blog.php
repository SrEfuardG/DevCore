<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevCore - Blog de Desarrollo de Software</title>
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


    <main class="blog-container">
        <section class="latest-posts">
            <?php
            include 'db_connection.php';

            // --- Lógica de Paginación ---
            $posts_per_page = 6; // Número de publicaciones por página
            $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($current_page - 1) * $posts_per_page;

            // Obtener la categoría seleccionada de la URL si existe
            $selected_category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : '';

            // Obtener el término de búsqueda de la URL si existe
            $search_query = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

            // --- Construir la consulta SQL para obtener el total de posts (para la paginación) ---
            $count_sql = "SELECT COUNT(*) AS total_posts FROM posts WHERE is_published = TRUE";
            $post_sql = "SELECT id, title, excerpt, image, published_date, author, external_url, post_type, category FROM posts WHERE is_published = TRUE";

            $where_clauses = [];

            // Añadir condición WHERE si hay una categoría seleccionada
            if (!empty($selected_category)) {
                $where_clauses[] = "category = '" . $conn->real_escape_string($selected_category) . "'";
            }

            // Añadir condición WHERE si hay un término de búsqueda
            if (!empty($search_query)) {
                $search_term_escaped = $conn->real_escape_string("%" . $search_query . "%");
                $where_clauses[] = "(title LIKE '{$search_term_escaped}' OR excerpt LIKE '{$search_term_escaped}' OR content LIKE '{$search_term_escaped}')";
            }

            // Combinar las cláusulas WHERE si existen para ambas consultas
            if (!empty($where_clauses)) {
                $where_clause_str = implode(" AND ", $where_clauses);
                $count_sql .= " AND " . $where_clause_str;
                $post_sql .= " AND " . $where_clause_str;
            }

            // Obtener el total de posts para calcular las páginas
            $total_posts_result = $conn->query($count_sql);
            $total_posts_row = $total_posts_result->fetch_assoc();
            $total_posts = $total_posts_row['total_posts'];
            $total_pages = ceil($total_posts / $posts_per_page); // Calcular el número total de páginas

            // Completar la consulta para obtener los posts de la página actual
            $post_sql .= " ORDER BY published_date DESC LIMIT {$posts_per_page} OFFSET {$offset}";

            $result = $conn->query($post_sql);
            ?>

            <h2>
                <?php
                if (!empty($selected_category)) {
                    echo "Publicaciones en: " . htmlspecialchars($selected_category);
                } elseif (!empty($search_query)) {
                    echo "Resultados para: '" . htmlspecialchars($search_query) . "'";
                } else {
                    echo "Últimas Publicaciones";
                }
                ?>
            </h2>

            <div class="posts-grid">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $post_link = '';
                        $target_blank = '';

                        if ($row['post_type'] === 'external' && !empty($row['external_url'])) {
                            $post_link = htmlspecialchars($row['external_url']);
                            $target_blank = ' target="_blank" rel="noopener noreferrer"';
                        } else {
                            $post_link = 'single-post.php?id=' . $row['id'];
                        }

                        echo '<article class="blog-post-card">';
                        echo '    <img src="img/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '">';
                        echo '    <div class="post-content">';
                        echo '        <h3>' . htmlspecialchars($row['title']) . '</h3>';
                        echo '        <p class="post-meta"><i class="far fa-calendar-alt"></i> ' . date("d F, Y", strtotime($row['published_date'])) . ' | <i class="far fa-user"></i> ' . htmlspecialchars($row['author']) . '</p>';
                        echo '        <p>' . htmlspecialchars($row['excerpt']) . '</p>';
                        echo '        <a href="' . $post_link . '" class="read-more"' . $target_blank . '>Leer más <i class="fas fa-arrow-right"></i></a>';
                        echo '    </div>';
                        echo '</article>';
                    }
                } else {
                    echo "<p>No hay publicaciones que coincidan con los criterios de búsqueda o categoría.</p>";
                }
                ?>
            </div>
            <div class="pagination">
                <?php
                // Construir la URL base para los enlaces de paginación
                $base_pagination_url = 'blog.php?';
                if (!empty($selected_category)) {
                    $base_pagination_url .= 'category=' . urlencode($selected_category) . '&';
                }
                if (!empty($search_query)) {
                    $base_pagination_url .= 'search=' . urlencode($search_query) . '&';
                }

                // Enlace "Anterior"
                if ($current_page > 1) {
                    echo '<a href="' . $base_pagination_url . 'page=' . ($current_page - 1) . '" class="page-link">&laquo; Anterior</a>';
                }

                // Enlaces de números de página
                for ($i = 1; $i <= $total_pages; $i++) {
                    $active_class = ($i === $current_page) ? ' active' : '';
                    echo '<a href="' . $base_pagination_url . 'page=' . $i . '" class="page-link' . $active_class . '">' . $i . '</a>';
                }

                // Enlace "Siguiente"
                if ($current_page < $total_pages) {
                    echo '<a href="' . $base_pagination_url . 'page=' . ($current_page + 1) . '" class="page-link">Siguiente &raquo;</a>';
                }
                ?>
            </div>
        </section>

        <aside class="sidebar">
            <div class="sidebar-widget search-widget">
                <h3>Buscar en el Blog</h3>
                <form action="blog.php" method="GET">
                    <input type="text" name="search" placeholder="Escribe para buscar..." aria-label="Buscar en el blog" value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                    <?php if (!empty($selected_category)): // Mantener la categoría seleccionada al buscar ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($selected_category); ?>">
                    <?php endif; ?>
                </form>
            </div>
            <div class="sidebar-widget categories-widget">
                <h3>Categorías</h3>
                <ul>
                    <li><a href="blog.php<?php echo !empty($search_query) ? '?search=' . urlencode($search_query) : ''; ?>" class="category-link<?php echo empty($selected_category) ? ' active' : ''; ?>">Todas las categorías</a></li>
                    <?php
                    // Consulta para obtener las categorías y el conteo de posts
                    // Asegúrate de que $conn esté disponible aquí, ya se incluyó db_connection.php
                    $sql_categories = "SELECT category, COUNT(*) AS post_count FROM posts WHERE is_published = TRUE GROUP BY category ORDER BY category ASC";
                    $result_categories = $conn->query($sql_categories);

                    if ($result_categories->num_rows > 0) {
                        while($cat_row = $result_categories->fetch_assoc()) {
                            $category_name = htmlspecialchars($cat_row['category']);
                            $post_count = htmlspecialchars($cat_row['post_count']);
                            // Resaltar la categoría activa
                            $active_class = ($selected_category === $category_name) ? ' active' : '';
                            
                            // Mantener el término de búsqueda al cambiar de categoría
                            $category_link_url = 'blog.php?category=' . urlencode($category_name);
                            if (!empty($search_query)) {
                                $category_link_url .= '&search=' . urlencode($search_query);
                            }

                            echo '<li><a href="' . $category_link_url . '" class="category-link' . $active_class . '">' . $category_name . ' (' . $post_count . ')</a></li>';
                        }
                    } else {
                        echo '<li>No hay categorías disponibles.</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="sidebar-widget popular-posts-widget">
                <h3>Posts Populares</h3>
                <ul>
                    <?php
                    // Consulta para posts populares (ejemplo: los 3 con más vistas)
                    $sql_popular = "SELECT id, title, external_url, post_type FROM posts WHERE is_published = TRUE ORDER BY views DESC LIMIT 3";
                    $result_popular = $conn->query($sql_popular);

                    if ($result_popular->num_rows > 0) {
                        while($pop_row = $result_popular->fetch_assoc()) {
                            $popular_post_link = '';
                            $popular_target_blank = '';

                            if ($pop_row['post_type'] === 'external' && !empty($pop_row['external_url'])) {
                                $popular_post_link = htmlspecialchars($pop_row['external_url']);
                                $popular_target_blank = ' target="_blank" rel="noopener noreferrer"';
                            } else {
                                $popular_post_link = 'single-post.php?id=' . $pop_row['id'];
                            }
                            echo '<li><a href="' . $popular_post_link . '"' . $popular_target_blank . '>' . htmlspecialchars($pop_row['title']) . '</a></li>';
                        }
                    } else {
                        echo '<li>No hay posts populares todavía.</li>';
                    }
                    ?>
                </ul>
            </div>
        </aside>
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
                    <li><a href="contacto.php">Contacto</a></li>
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
<?php $conn->close(); // Cierra la conexión a la BD al final de la página para liberar recursos ?>