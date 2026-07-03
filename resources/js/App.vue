<template>
  <div id="app">
    <Login v-if="!isAuthenticated" @login="handleLogin" />
    <Dashboard v-else @logout="handleLogout" />
  </div>
</template>

<script>
import axios from 'axios';
import Login from './components/Login.vue';
import Dashboard from './components/Dashboard.vue';

export default {
  name: 'App',
  components: {
    Login,
    Dashboard
  },
  data() {
    return {
      isAuthenticated: false
    };
  },
  async mounted() {
    await this.checkAuth();
  },
  methods: {
    async checkAuth() {
      try {
        const res = await axios.get('/api/me');
        this.isAuthenticated = !!res.data.user;
      } catch (e) {
        this.isAuthenticated = false;
      }
    },
    handleLogin() {
      this.checkAuth();
    },
    handleLogout() {
      this.isAuthenticated = false;
      localStorage.removeItem('token');
    }
  }
};
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
}

#app {
  min-height: 100vh;
}
</style>
