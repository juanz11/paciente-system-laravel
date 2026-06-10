<template>
  <div class="login-container">
    <div class="login-card">
      <div class="medical-logo">
        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
        </svg>
      </div>
      <h1 class="login-title">Sistema Médico</h1>
      <p class="login-subtitle">Gestión de Pacientes</p>
      
      <form @submit.prevent="login" class="login-form">
        <div class="form-group">
          <label for="username">Usuario</label>
          <input
            type="text"
            id="username"
            v-model="username"
            required
            placeholder="Ingrese su usuario"
          />
        </div>
        
        <div class="form-group">
          <label for="password">Contraseña</label>
          <input
            type="password"
            id="password"
            v-model="password"
            required
            placeholder="Ingrese su contraseña"
          />
        </div>
        
        <button type="submit" class="login-btn" :disabled="loading">
          <span v-if="loading">Iniciando sesión...</span>
          <span v-else>Iniciar Sesión</span>
        </button>
        
        <p v-if="error" class="error-message">{{ error }}</p>
      </form>
      
      <div class="login-footer">
        <p>Usuario: admin | Contraseña: admin</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Login',
  data() {
    return {
      username: '',
      password: '',
      loading: false,
      error: ''
    };
  },
  methods: {
    async login() {
      this.loading = true;
      this.error = '';
      
      try {
        const response = await axios.post('/api/login', {
          username: this.username,
          password: this.password
        });
        
        localStorage.setItem('token', 'authenticated');
        this.$emit('login');
      } catch (err) {
        this.error = 'Credenciales incorrectas';
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  padding: 20px;
}

.login-card {
  background: white;
  border-radius: 20px;
  padding: 40px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.medical-logo {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.login-title {
  text-align: center;
  color: #1e3c72;
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 5px;
}

.login-subtitle {
  text-align: center;
  color: #6c757d;
  font-size: 14px;
  margin-bottom: 30px;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  color: #1e3c72;
  font-weight: 600;
  font-size: 14px;
}

.form-group input {
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  font-size: 14px;
  transition: all 0.3s;
}

.form-group input:focus {
  outline: none;
  border-color: #2a5298;
  box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
}

.login-btn {
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  color: white;
  border: none;
  padding: 14px;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  margin-top: 10px;
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(30, 60, 114, 0.3);
}

.login-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error-message {
  color: #dc3545;
  text-align: center;
  font-size: 14px;
  margin-top: 10px;
}

.login-footer {
  margin-top: 30px;
  text-align: center;
  color: #6c757d;
  font-size: 12px;
  padding-top: 20px;
  border-top: 1px solid #e0e0e0;
}
</style>
