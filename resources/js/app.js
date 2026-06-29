import './bootstrap';

window.dashboardAnalytics = function() {
    return {
        days: 30,
        isLoading: true,
        stats: null,
        chartGrowth: null,
        chartVersion: null,
        chartDau: null,
        async initData() {
            await this.fetchData();
        },
        async fetchData(refresh = false) {
            this.isLoading = true;
            try {
                const response = await fetch(`/admin/api/dashboard/stats?days=${this.days}&refresh=${refresh}`);
                if (!response.ok) throw new Error('Failed to fetch stats');
                this.stats = await response.json();
                
                // Code Splitting: Dynamically load Chart.js only when needed!
                const { default: Chart } = await import('chart.js/auto');
                
                // Wait for Alpine to render the canvas elements, then draw charts
                setTimeout(() => {
                    this.renderCharts(Chart);
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }, 50);
            } catch (e) {
                console.error('Error loading analytics:', e);
            } finally {
                this.isLoading = false;
            }
        },
        renderCharts(Chart) {
            // Destroy old charts to prevent memory leaks or overlapping
            if (this.chartGrowth) this.chartGrowth.destroy();
            if (this.chartVersion) this.chartVersion.destroy();
            if (this.chartDau) this.chartDau.destroy();

            const ctxGrowth = this.$refs.canvasGrowth.getContext('2d');
            this.chartGrowth = new Chart(ctxGrowth, {
                type: 'line',
                data: {
                    labels: this.stats.growth.labels,
                    datasets: [{
                        label: 'Pengguna Baru',
                        data: this.stats.growth.data,
                        borderColor: '#4f46e5', // Indigo-600
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleFont: { size: 13, family: "'Inter', sans-serif" },
                            bodyFont: { size: 14, family: "'Inter', sans-serif" },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { family: "'Inter', sans-serif" } } },
                        y: { grid: { borderDash: [4, 4], color: '#e2e8f0' }, ticks: { font: { family: "'Inter', sans-serif" } } }
                    }
                }
            });

            const ctxVersion = this.$refs.canvasVersion.getContext('2d');
            this.chartVersion = new Chart(ctxVersion, {
                type: 'doughnut',
                data: {
                    labels: this.stats.version.labels,
                    datasets: [{
                        data: this.stats.version.data,
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#6366f1', '#14b8a6'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: { family: "'Inter', sans-serif", size: 12 }
                            }
                        }
                    }
                }
            });

            const ctxDau = this.$refs.canvasDau.getContext('2d');
            this.chartDau = new Chart(ctxDau, {
                type: 'bar',
                data: {
                    labels: this.stats.dau.labels,
                    datasets: [{
                        label: 'Pengguna Aktif (DAU)',
                        data: this.stats.dau.data,
                        backgroundColor: '#10b981', // Emerald-500
                        hoverBackgroundColor: '#059669', // Emerald-600
                        borderRadius: 4,
                        barPercentage: 0.6
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleFont: { size: 13, family: "'Inter', sans-serif" },
                            bodyFont: { size: 14, family: "'Inter', sans-serif" },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { family: "'Inter', sans-serif" } } },
                        y: { grid: { borderDash: [4, 4], color: '#e2e8f0' }, ticks: { font: { family: "'Inter', sans-serif" }, precision: 0 } }
                    }
                }
            });
        }
    };
};
