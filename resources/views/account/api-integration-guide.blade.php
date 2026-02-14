<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">API Integration - Complete User Guide</h1>
            <p class="mt-1 text-sm text-gray-500">Step-by-step instructions for connecting external applications to your Event Management System.</p>
        </div>

        {{-- Password Protection --}}
        <div x-data="{ 
            isUnlocked: false, 
            password: '', 
            showPassword: false,
            unlock() {
                if (this.password === 'password') {
                    this.isUnlocked = true;
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
            }
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

            {{-- Complete User Guide --}}
            <div x-show="isUnlocked" class="space-y-8">
                
                <!-- Quick Start Section -->
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6">
                    <div class="border-l-4 border-green-500 pl-4 mb-6">
                        <h2 class="text-xl font-bold text-gray-900">🚀 Quick Start for Beginners</h2>
                        <p class="text-gray-600 mt-1">Follow these 5 simple steps to connect your application to our API.</p>
                    </div>

                    <div class="space-y-6">
                        <!-- Step 1 -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">Register Your Application</h3>
                                <p class="text-gray-600 mb-3">First, you need to register your application to get API credentials.</p>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">What you need to provide:</p>
                                    <ul class="text-sm text-gray-600 space-y-1 ml-4">
                                        <li>• <strong>Application Name:</strong> A descriptive name for your app (e.g., "My Event Manager")</li>
                                        <li>• <strong>Description:</strong> Brief description of what your app does</li>
                                        <li>• <strong>Contact Email:</strong> Your email address for support</li>
                                        <li>• <strong>Website:</strong> Your app's website (optional)</li>
                                    </ul>
                                </div>
                                <div class="mt-3">
                                    <button onclick="document.getElementById('registerModal').classList.remove('hidden')" 
                                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                        Register Application Now
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">Save Your Credentials</h3>
                                <p class="text-gray-600 mb-3">After registration, you'll receive your API credentials. Save them securely!</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <p class="text-sm font-medium text-yellow-800 mb-2">⚠️ Important: Save These Credentials!</p>
                                    <div class="text-sm text-yellow-700">
                                        <p>You'll receive:</p>
                                        <ul class="ml-4 mt-1 space-y-1">
                                            <li>• <strong>App ID:</strong> <code class="bg-yellow-100 px-1 rounded">app_xxxxx</code></li>
                                            <li>• <strong>App Secret:</strong> <code class="bg-yellow-100 px-1 rounded">secret_xxxxx</code></li>
                                        </ul>
                                        <p class="mt-2 font-medium">These credentials are shown only once. Save them in a secure location!</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">Generate API Token</h3>
                                <p class="text-gray-600 mb-3">Use your credentials to generate an API token for authentication.</p>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">How to generate token:</p>
                                    <ol class="text-sm text-gray-600 space-y-2 ml-4">
                                        <li>1. Click "Generate Token" button</li>
                                        <li>2. Enter your App ID and App Secret</li>
                                        <li>3. Copy the generated token immediately</li>
                                        <li>4. Store it securely in your application</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">4</div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">Make Your First API Call</h3>
                                <p class="text-gray-600 mb-3">Test your connection by making a simple API request.</p>
                                <div class="bg-gray-900 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs text-gray-400">Example: Get All Events</span>
                                        <button onclick="copyCode('get-events')" class="text-gray-400 hover:text-white text-xs">Copy</button>
                                    </div>
                                    <pre id="get-events" class="text-sm text-gray-300 overflow-x-auto"><code>curl -X GET "http://ems-web.test/api/events" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"</code></pre>
                                </div>
                                <div class="mt-3">
                                    <a href="/api/developer" target="_blank" 
                                       class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        View More Examples →
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Step 5 -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">5</div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">Build Your Integration</h3>
                                <p class="text-gray-600 mb-3">Now you're ready to build your application using our API!</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-blue-50 rounded-lg p-3">
                                        <h4 class="font-medium text-blue-900 text-sm mb-1">📚 Documentation</h4>
                                        <p class="text-xs text-blue-700">Complete API reference</p>
                                        <a href="/API_DOCUMENTATION.md" class="text-xs text-blue-600 hover:underline">Read Docs →</a>
                                    </div>
                                    <div class="bg-green-50 rounded-lg p-3">
                                        <h4 class="font-medium text-green-900 text-sm mb-1">💻 Code Examples</h4>
                                        <p class="text-xs text-green-700">JavaScript, PHP, Python</p>
                                        <a href="/api/developer" target="_blank" class="text-xs text-green-600 hover:underline">View Examples →</a>
                                    </div>
                                    <div class="bg-purple-50 rounded-lg p-3">
                                        <h4 class="font-medium text-purple-900 text-sm mb-1">🆘 Support</h4>
                                        <p class="text-xs text-purple-700">Get help when needed</p>
                                        <a href="/api/integration/support" class="text-xs text-purple-600 hover:underline">Get Support →</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Instructions -->
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">📖 Detailed Instructions</h2>
                    
                    <div class="space-y-8">
                        <!-- Understanding the API -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 Understanding the API</h3>
                            <div class="prose max-w-none text-gray-600">
                                <p class="mb-4">The Event Management System API allows external applications to:</p>
                                <ul class="list-disc pl-6 space-y-2">
                                    <li><strong>Manage Events:</strong> Create, read, update, and delete events</li>
                                    <li><strong>Handle Finances:</strong> Manage budgets and financial requests</li>
                                    <li><strong>Track Logistics:</strong> Manage resources and materials</li>
                                    <li><strong>Monitor Participants:</strong> Add and manage event participants</li>
                                    <li><strong>Receive Updates:</strong> Get real-time notifications via webhooks</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Authentication Guide -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔐 Authentication Explained</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-3">How Authentication Works:</h4>
                                <ol class="space-y-3 text-sm text-gray-600">
                                    <li>
                                        <strong>Step 1:</strong> Register your application to get App ID and App Secret
                                    </li>
                                    <li>
                                        <strong>Step 2:</strong> Use these credentials to generate an API token
                                    </li>
                                    <li>
                                        <strong>Step 3:</strong> Include the token in every API request using the Authorization header
                                    </li>
                                    <li>
                                        <strong>Step 4:</strong> The API validates your token and processes your request
                                    </li>
                                </ol>
                                
                                <div class="mt-4">
                                    <h5 class="font-medium text-gray-900 mb-2">Authorization Header Format:</h5>
                                    <div class="bg-gray-900 rounded p-2 text-xs text-gray-300">
                                        Authorization: Bearer your_api_token_here
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Common Use Cases -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">💼 Common Use Cases</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-2">📱 Mobile App Integration</h4>
                                    <p class="text-sm text-gray-600 mb-3">Connect your mobile app to manage events on the go.</p>
                                    <div class="text-xs text-gray-500">
                                        <p>Perfect for:</p>
                                        <ul class="ml-4 mt-1">
                                            <li>• Event registration apps</li>
                                            <li>• Mobile check-in systems</li>
                                            <li>• Event scheduling apps</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-2">🌐 Website Integration</h4>
                                    <p class="text-sm text-gray-600 mb-3">Display events on your website with real-time updates.</p>
                                    <div class="text-xs text-gray-500">
                                        <p>Perfect for:</p>
                                        <ul class="ml-4 mt-1">
                                            <li>• Event listing websites</li>
                                            <li>• Registration portals</li>
                                            <li>• Corporate intranets</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-2">📊 Analytics Dashboard</h4>
                                    <p class="text-sm text-gray-600 mb-3">Build custom dashboards for event analytics.</p>
                                    <div class="text-xs text-gray-500">
                                        <p>Perfect for:</p>
                                        <ul class="ml-4 mt-1">
                                            <li>• Management dashboards</li>
                                            <li>• Reporting tools</li>
                                            <li>• Business intelligence</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-2">🔄 Automation Tools</h4>
                                    <p class="text-sm text-gray-600 mb-3">Automate event management workflows.</p>
                                    <div class="text-xs text-gray-500">
                                        <p>Perfect for:</p>
                                        <ul class="ml-4 mt-1">
                                            <li>• Automated notifications</li>
                                            <li>• Data synchronization</li>
                                            <li>• Workflow automation</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Error Handling -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">⚠️ Error Handling</h3>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <h4 class="font-medium text-red-900 mb-3">Common Errors and Solutions:</h4>
                                <div class="space-y-3 text-sm">
                                    <div>
                                        <strong>401 Unauthorized:</strong>
                                        <p class="text-red-700">Your API token is invalid or expired. Generate a new token.</p>
                                    </div>
                                    <div>
                                        <strong>403 Forbidden:</strong>
                                        <p class="text-red-700">You don't have permission to access this resource.</p>
                                    </div>
                                    <div>
                                        <strong>422 Validation Error:</strong>
                                        <p class="text-red-700">Your request data is invalid. Check the error messages for details.</p>
                                    </div>
                                    <div>
                                        <strong>429 Too Many Requests:</strong>
                                        <p class="text-red-700">You've exceeded the rate limit. Wait before making more requests.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Best Practices -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">✅ Best Practices</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2">🔒 Security</h4>
                                    <ul class="text-sm text-gray-600 space-y-1">
                                        <li>• Never expose API tokens in client-side code</li>
                                        <li>• Use environment variables for sensitive data</li>
                                        <li>• Regenerate tokens if they might be compromised</li>
                                        <li>• Implement proper error handling</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2">⚡ Performance</h4>
                                    <ul class="text-sm text-gray-600 space-y-1">
                                        <li>• Use pagination for large datasets</li>
                                        <li>• Cache frequently accessed data</li>
                                        <li>• Handle rate limits gracefully</li>
                                        <li>• Use appropriate HTTP methods</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help and Support -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white">
                    <h2 class="text-xl font-bold mb-4">🆘 Need Help?</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="font-semibold mb-2">📚 Documentation</h3>
                            <p class="text-sm text-indigo-100 mb-3">Complete API reference and guides</p>
                            <a href="/API_DOCUMENTATION.md" class="inline-flex items-center text-sm font-medium text-white hover:text-indigo-100">
                                Read Documentation →
                            </a>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">💬 Community</h3>
                            <p class="text-sm text-indigo-100 mb-3">Join our developer community</p>
                            <a href="/api/integration/support" class="inline-flex items-center text-sm font-medium text-white hover:text-indigo-100">
                                Join Community →
                            </a>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">📧 Email Support</h3>
                            <p class="text-sm text-indigo-100 mb-3">Get help from our support team</p>
                            <a href="mailto:api-support@ems-web.test" class="inline-flex items-center text-sm font-medium text-white hover:text-indigo-100">
                                Contact Support →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Register Your Application</h3>
                <form id="registerForm">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Application Name</label>
                            <input type="text" name="app_name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="e.g., My Event Manager">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" required rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Brief description of your application"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                            <input type="email" name="contact_email" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="your@email.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Website (Optional)</label>
                            <input type="url" name="website"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="https://yourapp.com">
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('registerModal').classList.add('hidden')"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            Register Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function copyCode(elementId) {
            const code = document.getElementById(elementId).textContent;
            navigator.clipboard.writeText(code).then(() => {
                // Show success feedback
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.classList.add('text-green-400');
                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('text-green-400');
                }, 2000);
            });
        }

        // Handle form submission
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
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
                    alert('🎉 Application registered successfully!\n\n' +
                          'IMPORTANT: Save these credentials securely!\n\n' +
                          'App ID: ' + result.data.app_id + '\n' +
                          'App Secret: ' + result.data.app_secret + '\n\n' +
                          'These credentials are shown only once!');
                    document.getElementById('registerModal').classList.add('hidden');
                    e.target.reset();
                } else {
                    alert('❌ Registration failed: ' + result.message);
                }
            } catch (error) {
                alert('❌ Error: ' + error.message);
            }
        });
    </script>
</x-app-layout>
