<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Publicaciones y Tickets</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-bullhorn"></i> Panel Admin
            </div>
            <ul class="sidebar-menu">
                <li class="active"><i class="fas fa-newspaper"></i> Publicaciones</li>
                <li><i class="fas fa-ticket-alt"></i> Tickets</li>
                <li><i class="fas fa-users"></i> Usuarios</li>
                <li><i class="fas fa-cogs"></i> Configuración</li>
                <li><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <h1 class="page-title">Gestión de Publicaciones y Tickets</h1>
            </div>

            <!-- Search Bar -->
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" class="search-input" placeholder="Buscar...">
            </div>

            <!-- Navegación de pestañas -->
            <div class="nav-tabs">
                <button class="tab-btn active" onclick="showSection('publications')">
                    <i class="fas fa-newspaper"></i> Publicaciones
                </button>
               
            </div>

            <!-- Sección de Publicaciones -->
            <div id="publications" class="section active">
                <div class="card-container">
                    <div class="content-header">
                        <h2>Lista de Publicaciones</h2>
                        <button id="btnNewPublication" class="btn-new" onclick="showModal('publicationModal')">
                            <i class="fas fa-plus"></i> Nueva Publicación
                        </button>
                    </div>

                    <div class="table-wrapper">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Plataforma</th>
                                    <th>Enlace</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="publicationsList">
                                <!-- Se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sección de Publicaciones-Tickets -->
            <div id="tickets" class="section">
                <div class="card-container">
                    <div class="content-header">
                        <h2>Lista de Relaciones Publicación-Ticket</h2>
                        <button id="btnNewRelation" class="btn-new" onclick="showModal('relationModal')">
                            <i class="fas fa-plus"></i> Nueva Relación
                        </button>
                    </div>

                    <div class="table-wrapper">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Publicación</th>
                                    <th>ID Ticket</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="publicationsTicketsList">
                                <!-- Se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para formulario de Publicación -->
    <div class="modal" id="publicationModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Publicación</h5>
                    <button type="button" class="close-btn" onclick="closeModal('publicationModal')">&times;</button>
                </div>
                <form id="publicationForm">
                    <div class="modal-body">
                        <input type="hidden" name="id_publicacion" id="id_publicacion">
                        <div class="form-group">
                            <label for="plataforma">Plataforma:</label>
                            <select name="plataforma" id="plataforma" required>
                                <option value="Web">Web</option>
                                <option value="Redes Sociales">Redes Sociales</option>
                                <option value="Impreso">Impreso</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="enlace">Enlace:</label>
                            <input type="text" name="enlace" id="enlace">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" onclick="closeModal('publicationModal')">Cancelar</button>
                        <button type="submit" class="btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para formulario de Relación Publicación-Ticket -->
    <div class="modal" id="relationModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Relación</h5>
                    <button type="button" class="close-btn" onclick="closeModal('relationModal')">&times;</button>
                </div>
                <form id="publicationTicketForm">
                    <div class="modal-body">
                        <input type="hidden" name="id_relacion" id="id_relacion">
                        <div class="form-group">
                            <label for="id_publicacion_rel">Publicación:</label>
                            <select name="id_publicacion" id="id_publicacion_rel" required>
                                <!-- Se llenará dinámicamente -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_ticket">ID del Ticket:</label>
                            <input type="number" name="id_ticket" id="id_ticket" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" onclick="closeModal('relationModal')">Cancelar</button>
                        <button type="submit" class="btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>