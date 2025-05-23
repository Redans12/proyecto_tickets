class UserManager {
    constructor() {
        // Mantenemos la misma URL del API, la lógica de adaptación está en el servidor
        this.apiUrl = 'http://localhost/proyecto_tickets/gestion_tickets/gestion_tickets/api/users.php';
        this.initializeEventListeners();
        this.loadUsers();
    }

    initializeEventListeners() {
        document.getElementById('btnNewUser').addEventListener('click', () => {
            this.showUserModal();
        });

        document.getElementById('userForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveUser();
        });
    }

    async loadUsers() {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            if (result.success) {
                this.displayUsers(result.data);
            } else {
                throw new Error(result.message || 'Error al cargar usuarios');
            }
        } catch (error) {
            this.showNotification('Error al cargar usuarios: ' + error.message, 'error');
            console.error('Error completo:', error);
        }
    }

    displayUsers(users) {
        // Datos de prueba para verificar si la función de visualización funciona
        if (!users || users.length === 0) {
            console.log('No hay usuarios o la estructura es incorrecta, usando datos de prueba');
            users = [
                {
                    id_usuario: 1,
                    nombre: 'Usuario de Prueba',
                    email: 'test@example.com',
                    rol: 'Admin',
                    fecha_creacion: '2025-05-02 12:00:00'
                }
            ];
        }



        const tbody = document.getElementById('usersTableBody');
        tbody.innerHTML = '';

        users.forEach(user => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${user.id_usuario}</td>
                <td>${this.escapeHtml(user.nombre)}</td>
                <td>${this.escapeHtml(user.email)}</td>
                <td><span class="badge ${user.rol === 'Cliente' ? 'bg-primary' : 'bg-success'}">${user.rol}</span></td>
                <td>${new Date(user.fecha_creacion).toLocaleString()}</td>
                <td>
                    <button class="btn btn-sm btn-warning me-2" onclick="userManager.editUser(${user.id_usuario})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="userManager.deleteUser(${user.id_usuario})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    showUserModal(userData = null) {
        const modal = document.getElementById('userModal');
        const form = document.getElementById('userForm');
        form.reset();

        if (userData) {
            document.getElementById('userId').value = userData.id_usuario;
            document.getElementById('nombre').value = userData.nombre;
            document.getElementById('email').value = userData.email;
            document.getElementById('rol').value = userData.rol;
            document.getElementById('modalTitle').textContent = 'Editar Usuario';
            document.getElementById('password').required = false;
        } else {
            document.getElementById('userId').value = '';
            document.getElementById('modalTitle').textContent = 'Nuevo Usuario';
            document.getElementById('password').required = true;
        }

        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }

    async saveUser() {
        const form = document.getElementById('userForm');
        const userId = document.getElementById('userId').value;

        const userData = {
            nombre: document.getElementById('nombre').value,
            email: document.getElementById('email').value,
            rol: document.getElementById('rol').value
        };

        const password = document.getElementById('password').value;
        if (password) {
            userData.contrasena = password;
        }

        try {
            const method = userId ? 'PUT' : 'POST';
            const url = userId ? `${this.apiUrl}?id=${userId}` : this.apiUrl;

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(userData)
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification(
                    userId ? 'Usuario actualizado con éxito' : 'Usuario creado con éxito',
                    'success'
                );
                bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
                this.loadUsers();
            } else {
                throw new Error(result.message || 'Error en la operación');
            }
        } catch (error) {
            this.showNotification('Error: ' + error.message, 'error');
            console.error('Error completo:', error);
        }
    }

    async editUser(id) {
        try {
            const response = await fetch(`${this.apiUrl}?id=${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            if (result.success) {
                this.showUserModal(result.data);
            } else {
                throw new Error(result.message || 'Error al cargar el usuario');
            }
        } catch (error) {
            this.showNotification('Error al cargar el usuario: ' + error.message, 'error');
            console.error('Error completo:', error);
        }
    }

    async deleteUser(id) {
        if (!confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
            return;
        }

        try {
            const response = await fetch(`${this.apiUrl}?id=${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            if (result.success) {
                this.showNotification('Usuario eliminado con éxito', 'success');
                this.loadUsers();
            } else {
                throw new Error(result.message || 'Error al eliminar el usuario');
            }
        } catch (error) {
            this.showNotification('Error al eliminar el usuario: ' + error.message, 'error');
            console.error('Error completo:', error);
        }
    }

    showNotification(message, type) {
        const alertContainer = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${this.escapeHtml(message)}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertContainer.appendChild(alert);
        setTimeout(() => alert.remove(), 5000);
    }

    escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
}

let userManager;
document.addEventListener('DOMContentLoaded', () => {
    userManager = new UserManager();
});