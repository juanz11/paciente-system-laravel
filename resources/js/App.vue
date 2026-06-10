<template>
  <div id="app">
    <Login v-if="!isAuthenticated" @login="handleLogin" />
    <Dashboard v-else @logout="handleLogout" />
  </div>
</template>

<script>
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
  mounted() {
    const token = localStorage.getItem('token');
    this.isAuthenticated = !!token;
  },
  methods: {
    handleLogin() {
      this.isAuthenticated = true;
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
