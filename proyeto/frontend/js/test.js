
const API_URL = '../../backend/api/edpoint.php';

// Function to get all users
const getUsuarios = () => {
    fetch(API_URL)
        .then(response => response.json())
        .then(data => console.log('Get All Users:', data))
        .catch(error => console.error('Error:', error));
};

// Function to get a single user
const getUsuario = (id) => {
    fetch(`${API_URL}?id=${id}`)
        .then(response => response.json())
        .then(data => console.log(`Get User ${id}:`, data))
        .catch(error => console.error('Error:', error));
};

// Function to create a user
const createUsuario = (usuario) => {
    fetch(API_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(usuario)
    })
        .then(response => response.json())
        .then(data => console.log('Create User:', data))
        .catch(error => console.error('Error:', error));
};

// Function to update a user
const updateUsuario = (id, usuario) => {
    fetch(`${API_URL}?id=${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(usuario)
    })
        .then(response => response.json())
        .then(data => console.log('Update User:', data))
        .catch(error => console.error('Error:', error));
};

// Function to delete a user
const deleteUsuario = (id) => {
    fetch(`${API_URL}?id=${id}`, {
        method: 'DELETE'
    })
        .then(response => response.json())
        .then(data => console.log('Delete User:', data))
        .catch(error => console.error('Error:', error));
};

// Example Usage
console.log('Running API tests...');

// Create a new user
const newUser = {
    nombre: 'John Doe',
    correo: 'john.doe@example.com',
    telefono: '1234567890'
};
createUsuario(newUser);

// Get all users
setTimeout(getUsuarios, 1000);

// Get a single user (e.g., user with id 1)
setTimeout(() => getUsuario(1), 2000);

// Update a user (e.g., user with id 1)
const updatedUser = {
    nombre: 'John Smith',
    correo: 'john.smith@example.com',
    telefono: '0987654321'
};
setTimeout(() => updateUsuario(1, updatedUser), 3000);

// Get all users again to see the update
setTimeout(getUsuarios, 4000);

// Delete a user (e.g., user with id 1)
setTimeout(() => deleteUsuario(1), 5000);

// Get all users one last time
setTimeout(getUsuarios, 6000);
