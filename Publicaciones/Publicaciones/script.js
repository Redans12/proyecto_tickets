// Funciones para el CRUD de Publicaciones y Publicaciones-Tickets
document.addEventListener('DOMContentLoaded', function() {
    loadPublications();
    loadPublicationsTickets();
    
    // Event listeners para formularios
    document.getElementById('publicationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        savePublication();
    });
    
    document.getElementById('publicationTicketForm').addEventListener('submit', function(e) {
        e.preventDefault();
        savePublicationTicket();
    });
});

// Funciones de navegación
function showSection(sectionId) {
    document.querySelectorAll('.section').forEach(section => {
        section.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    document.getElementById(sectionId).classList.add('active');
    const activeBtn = document.querySelector(`[onclick="showSection('${sectionId}')"]`);
    if (activeBtn) {
        activeBtn.classList.add('active');
    }
}

function showModal(modalId) {
    document.getElementById(modalId).classList.add('show');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
    if (modalId === 'publicationModal') {
        resetForm('publicationForm');
    } else if (modalId === 'relationModal') {
        resetForm('publicationTicketForm');
    }
}

// Funciones para Publicaciones
function loadPublications() {
    fetch('get_publications.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('publicationsList');
            tbody.innerHTML = '';
            
            data.forEach(pub => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${pub.id_publicacion}</td>
                    <td>${pub.plataforma}</td>
                    <td>${pub.enlace ? `<a href="${pub.enlace}" target="_blank">${pub.enlace}</a>` : '-'}</td>
                    <td>${pub.fecha_publicacion}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-icon edit" onclick="editPublication(${pub.id_publicacion})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon delete" onclick="deletePublication(${pub.id_publicacion})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
            updatePublicationsSelect();
        })
        .catch(error => console.error('Error:', error));
}

function savePublication() {
    const formData = new FormData(document.getElementById('publicationForm'));
    const id = document.getElementById('id_publicacion').value;
    const url = id ? 'update_publication.php' : 'create_publication.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            loadPublications();
            closeModal('publicationModal');
        } else {
            alert('Error: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => console.error('Error:', error));
}

function editPublication(id) {
    fetch(`get_publication.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('id_publicacion').value = data.id_publicacion;
            document.getElementById('plataforma').value = data.plataforma;
            document.getElementById('enlace').value = data.enlace || '';
            showModal('publicationModal');
        })
        .catch(error => console.error('Error:', error));
}

function deletePublication(id) {
    if(confirm('¿Está seguro de eliminar esta publicación?')) {
        fetch(`delete_publication.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    loadPublications();
                } else {
                    alert('Error al eliminar la publicación');
                }
            })
            .catch(error => console.error('Error:', error));
    }
}

// Funciones para Publicaciones-Tickets
function loadPublicationsTickets() {
    fetch('get_publications_tickets.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('publicationsTicketsList');
            tbody.innerHTML = '';
            
            data.forEach(rel => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${rel.id_relacion}</td>
                    <td>${rel.publicacion}</td>
                    <td>${rel.id_ticket}</td>
                    <td>${rel.fecha_relacion}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-icon edit" onclick="editPublicationTicket(${rel.id_relacion})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon delete" onclick="deletePublicationTicket(${rel.id_relacion})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error:', error));
}

function savePublicationTicket() {
    const formData = new FormData(document.getElementById('publicationTicketForm'));
    const id = document.getElementById('id_relacion').value;
    const url = id ? 'update_publication_ticket.php' : 'create_publication_ticket.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            loadPublicationsTickets();
            closeModal('relationModal');
        } else {
            alert('Error: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => console.error('Error:', error));
}

function editPublicationTicket(id) {
    fetch(`get_publication_ticket.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('id_relacion').value = data.id_relacion;
            document.getElementById('id_publicacion_rel').value = data.id_publicacion;
            document.getElementById('id_ticket').value = data.id_ticket;
            showModal('relationModal');
        })
        .catch(error => console.error('Error:', error));
}
function deletePublicationTicket(id) {
    if(confirm('¿Está seguro de eliminar esta relación?')) {
        fetch(`delete_publication_ticket.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    loadPublicationsTickets();
                    alert('Relación eliminada correctamente');
                } else {
                    alert('Error: ' + (data.message || 'Error al eliminar la relación'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
    }
}

function updatePublicationsSelect() {
    fetch('get_publications.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('id_publicacion_rel');
            select.innerHTML = '<option value="">Seleccione una publicación</option>';
            data.forEach(pub => {
                select.innerHTML += `
                    <option value="${pub.id_publicacion}">
                        ${pub.plataforma} - ${pub.enlace || 'Sin enlace'}
                    </option>
                `;
            });
        })
        .catch(error => console.error('Error:', error));
}

function resetForm(formId) {
    document.getElementById(formId).reset();
    if(formId === 'publicationForm') {
        document.getElementById('id_publicacion').value = '';
    } else if(formId === 'publicationTicketForm') {
        document.getElementById('id_relacion').value = '';
    }
}