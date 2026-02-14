<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">API Integration</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your API applications and tokens for external integrations.</p>
        </div>

        {{-- Password Protection --}}
        <div x-data="{ 
            isUnlocked: false, 
            password: '', 
            showPassword: false,
            unlock() {
                if (this.password === 'password') {
                    this.isUnlocked = true;
                    this.loadApiData();
                } else {
                    this.showError();
                }
            },
            showError() {
                const errorEl = document.getElementById('password-error');
                errorEl.classList.remove('hidden');
                setTimeout(() => {
                    errorEl.classList.add('hidden');
                }, 3000);
            },
            async loadApiData() {
                // Load user's API applications and tokens
                this.applications = [
                    {
                        id: 1,
                        name: 'My Event Manager',
                        description: 'Application for managing events',
                        app_id: 'app_demo_123',
                        status: 'active',
                        created_at: new Date().toISOString(),
                        tokens: [
                            {
                                id: 1,
                                name: 'Production Token',
                                created_at: new Date().toISOString(),
                                last_used: new Date().toISOString()
                            }
                        ]
                    }
                ];
            },
            applications: [],
            showRegisterModal: false,
            showTokenModal: false,
            selectedApp: null
        }">

            {{-- Password Entry --}}
            <div x-show="!isUnlocked" class="max-w-md mx-auto">
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6">
                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">API Integration Access</h2>
                        <p class="text-sm text-gray-600 mb-6">Enter your password to access API management features.</p>
                        
                        <form @submit.prevent="unlock()">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <div class="relative">
                                    <input 
                                        x-model="password"
                                        :type="showPassword ? 'text' : 'password'"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10"
                                        placeholder="Enter password"
                                        required>
                                    <button 
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <svg x-show="!showPassword" class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="showPassword" class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <button 
                                type="submit"
                                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition font-medium">
                                Access API Management
                            </button>
                        </form>
                        
                        <div id="password-error" class="hidden mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-600">Incorrect password. Please try again.</p>
                        </div>
                        
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>Default Password:</strong> <code class="bg-blue-100 px-1 rounded">password</code><br>
                                <span class="text-xs">For security, change this password in production.</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- API Management Content --}}
            <div x-show="isUnlocked" class="space-y-6">
                {{-- Quick Stats --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-indigo-100 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Applications</p>
                                <p class="text-2xl font-semibold" x-text="applications.length || 0">0</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Active Tokens</p>
                                <p class="text-2xl font-semibold">3</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">API Calls</p>
                                <p class="text-2xl font-semibold">1.2K</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-orange-100 rounded-lg">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Last Activity</p>
                                <p class="text-2xl font-semibold">2h</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">API Management</h2>
                        <div class="flex gap-3">
                            <button 
                                @click="showRegisterModal = true"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                New Application
                            </button>
                            <a href="/api/developer" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                API Documentation
                            </a>
                        </div>
                    </div>

                    {{-- Applications List --}}
                    <div class="space-y-4">
                        <template x-for="app in applications" :key="app.id">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="font-semibold text-gray-900" x-text="app.name"></h3>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                  :class="app.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                                  x-text="app.status"></span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3" x-text="app.description"></p>
                                        <div class="flex items-center gap-6 text-sm text-gray-500">
                                            <span>App ID: <code class="bg-gray-100 px-1 rounded" x-text="app.app_id"></code></span>
                                            <span>Created: <span x-text="new Date(app.created_at).toLocaleDateString()"></span></span>
                                            <span>Tokens: <span x-text="app.tokens?.length || 0"></span></span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button 
                                            @click="selectedApp = app; showTokenModal = true"
                                            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                            Manage Tokens
                                        </button>
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="applications.length === 0" class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No applications yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first API application.</p>
                            <div class="mt-6">
                                <button 
                                    @click="showRegisterModal = true"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                                    Create Application
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Register Modal -->
                <div x-show="showRegisterModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 overflow-y-auto"
                     style="display: none;">
                    <div class="flex min-h-screen items-center justify-center p-4">
                        <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Register New Application</h3>
                                <button @click="showRegisterModal = false" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            <form @submit.prevent="registerApplication($event)">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Application Name</label>
                                        <input type="text" name="app_name" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea name="description" required rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Website (Optional)</label>
                                        <input type="url" name="website"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                                
                                <div class="mt-6 flex justify-end gap-3">
                                    <button type="button" @click="showRegisterModal = false"
                                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                        Register
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Token Modal -->
                <div x-show="showTokenModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 overflow-y-auto"
                     style="display: none;">
                    <div class="flex min-h-screen items-center justify-center p-4">
                        <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Manage API Tokens</h3>
                                <button @click="showTokenModal = false" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            <div x-show="selectedApp" class="space-y-4">
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-600">Application: <span class="font-medium" x-text="selectedApp?.name"></span></p>
                                    <p class="text-sm text-gray-600">App ID: <code class="bg-gray-100 px-1 rounded" x-text="selectedApp?.app_id"></code></p>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Production Token</p>
                                            <p class="text-xs text-gray-500">Created: Jan 15, 2024</p>
                                        </div>
                                        <button class="text-indigo-600 hover:text-indigo-800 text-sm">Regenerate</button>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Development Token</p>
                                            <p class="text-xs text-gray-500">Created: Jan 10, 2024</p>
                                        </div>
                                        <button class="text-indigo-600 hover:text-indigo-800 text-sm">Regenerate</button>
                                    </div>
                                </div>
                                
                                <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                    Generate New Token
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Remove API token meta tag as we'll handle it differently -->
    <script>
        function apiIntegrationManager() {
            return {
                async registerApplication(event) {
                    const formData = new FormData(event.target);
                    const data = Object.fromEntries(formData);
                    
                    try {
                        const response = await fetch('/api/integration/register', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(data)
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            alert('Application registered successfully!\n\nApp ID: ' + result.data.app_id + '\nApp Secret: ' + result.data.app_secret);
                            this.showRegisterModal = false;
                            this.loadApiData();
                            event.target.reset();
                        } else {
                            alert('Registration failed: ' + result.message);
                        }
                    } catch (error) {
                        alert('Error: ' + error.message);
                    }
                }
            }
        }
    </script>
</x-app-layout>
