@extends('admin.layouts')

@section('page-title', 'Customer Details - ' . $customer->full_name)
@section('page-subtitle', 'Comprehensive customer profile and activity management')

@section('content')
<style>
    /* Customer Detail Page Styles */
    .customer-detail-header {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .customer-profile {
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .customer-profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        font-weight: 600;
        overflow: hidden;
        position: relative;
    }

    .customer-profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .customer-profile-info h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0 0 0.5rem 0;
    }

    .customer-profile-info .customer-meta {
        display: flex;
        gap: 2rem;
        color: #718096;
        font-size: 0.95rem;
        margin-bottom: 1rem;
    }

    .customer-profile-badges {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .profile-badge {
        padding: 0.375rem 1rem;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .profile-badge.success {
        background: #dcfce7;
        color: #166534;
    }

    .profile-badge.danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .profile-badge.warning {
        background: #fef3c7;
        color: #92400e;
    }

    .profile-badge.info {
        background: #dbeafe;
        color: #1e40af;
    }

    .quick-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .quick-action-btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
    }

    .quick-action-btn.primary {
        background: #667eea;
        color: white;
    }

    .quick-action-btn.primary:hover {
        background: #5a67d8;
        color: white;
        text-decoration: none;
    }

    .quick-action-btn.secondary {
        background: #e2e8f0;
        color: #4a5568;
    }

    .quick-action-btn.secondary:hover {
        background: #cbd5e0;
        color: #2d3748;
        text-decoration: none;
    }

    .quick-action-btn.danger {
        background: #ef4444;
        color: white;
    }

    .quick-action-btn.danger:hover {
        background: #dc2626;
        color: white;
        text-decoration: none;
    }

    .quick-action-btn.success {
        background: #22c55e;
        color: white;
    }

    .quick-action-btn.success:hover {
        background: #16a34a;
        color: white;
        text-decoration: none;
    }

    /* Stats Overview */
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-overview-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .stat-overview-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .stat-overview-label {
        color: #718096;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .stat-overview-change {
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    .stat-overview-change.positive {
        color: #22c55e;
    }

    .stat-overview-change.negative {
        color: #ef4444;
    }

    /* Tabs */
    .detail-tabs {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .tab-nav {
        display: flex;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        overflow-x: auto;
    }

    .tab-nav-item {
        padding: 1rem 1.5rem;
        background: none;
        border: none;
        color: #718096;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .tab-nav-item:hover {
        color: #4a5568;
        background: rgba(102, 126, 234, 0.1);
    }

    .tab-nav-item.active {
        color: #667eea;
        background: white;
        border-bottom: 3px solid #667eea;
    }

    .tab-content {
        display: none;
        padding: 2rem;
    }

    .tab-content.active {
        display: block;
    }

    /* Tables */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .data-table th,
    .data-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }

    .data-table th {
        background: #f8fafc;
        font-weight: 600;
        color: #4a5568;
    }

    .data-table tr:hover {
        background: #f8fafc;
    }

    /* Order status */
    .order-status {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .order-status.completed {
        background: #dcfce7;
        color: #166534;
    }

    .order-status.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .order-status.cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Empty states */
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #718096;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .customer-profile {
            flex-direction: column;
            text-align: center;
        }

        .customer-profile-info .customer-meta {
            flex-direction: column;
            gap: 0.5rem;
        }

        .quick-actions {
            justify-content: center;
        }

        .stats-overview {
            grid-template-columns: repeat(2, 1fr);
        }

        .tab-nav {
            flex-direction: column;
        }
    }
</style>

<!-- Customer Detail Content -->
<div class="customer-detail-container">
    <!-- Back Button -->
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('admin_get_customers') }}" class="quick-action-btn secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Customers
        </a>
    </div>

    <!-- Customer Profile Header -->
    <div class="customer-detail-header">
        <div class="customer-profile">
            <div class="customer-profile-avatar">
                @php
                // Get default image from app settings
                $customerImageSetting = \App\Models\AppSetting::where("key", "=", "customer-image")->first();
                $defaultImageName = $customerImageSetting ? $customerImageSetting->value : '';

                // Determine which image to show
                $imageToShow = null;
                if ($customer->picture && $customer->picture !== '') {
                $imageToShow = $customer->picture;
                } elseif ($defaultImageName) {
                $imageToShow = $defaultImageName;
                }
                @endphp

                @if($imageToShow)
                <img src="{{ asset('storage/images/' . $imageToShow) }}" alt="{{ $customer->full_name }}" />
                @else
                {{ strtoupper(substr($customer->full_name, 0, 2)) }}
                @endif
            </div>
            <div class="customer-profile-info">
                <h1>{{ $customer->full_name }}</h1>
                <div class="customer-meta">
                    <div><i class="fas fa-envelope"></i> {{ $customer->email }}</div>
                    <div><i class="fas fa-phone"></i> {{ $customer->phone ?: 'No phone provided' }}</div>
                    @if($customer->city || $customer->country)
                    <div><i class="fas fa-map-marker-alt"></i> {{ $customer->city }}, {{ $customer->country }}</div>
                    @endif
                    <div><i class="fas fa-calendar"></i> Joined {{ $customer->created_at->format('F d, Y') }}</div>
                </div>
                <div class="customer-profile-badges">
                    {{-- FIXED: Single badge logic to prevent duplicates --}}
                    @if($customer->blocked)
                    <span class="profile-badge danger">
                        <i class="fas fa-ban"></i> Blocked
                    </span>
                    @elseif(!$customer->active)
                    <span class="profile-badge warning">
                        <i class="fas fa-pause"></i> Inactive
                    </span>
                    @elseif($customer->verified)
                    <span class="profile-badge success">
                        <i class="fas fa-certificate"></i> Verified Customer
                    </span>
                    @else
                    <span class="profile-badge info">
                        <i class="fas fa-user"></i> Active Customer
                    </span>
                    @endif
                    {{-- Additional badges that can appear together --}}
                    @if($customer->total_spent > 1000)
                    <span class="profile-badge warning">
                        <i class="fas fa-crown"></i> VIP Customer
                    </span>
                    @endif

                    <span class="profile-badge info">
                        <i class="fas fa-calendar"></i> {{ $customer->account_age_days }} days old
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="{{ route('admin_edit_customer_form', $customer) }}" class="quick-action-btn primary">
                <i class="fas fa-edit"></i>
                Edit Profile
            </a>

            @if($customer->blocked)
            <form method="POST" action="{{ route('admin_toggle_customer_block', $customer) }}" style="display: inline;">
                @csrf
                <button type="submit" class="quick-action-btn success">
                    <i class="fas fa-unlock"></i>
                    Unblock Customer
                </button>
            </form>
            @else
            <form method="POST" action="{{ route('admin_toggle_customer_block', $customer) }}" style="display: inline;">
                @csrf
                <button type="submit" class="quick-action-btn danger" onclick="return confirm('Are you sure you want to block this customer?')">
                    <i class="fas fa-ban"></i>
                    Block Customer
                </button>
            </form>
            @endif

            @if($customer->active)
            <form method="POST" action="{{ route('admin_toggle_customer_activation', $customer) }}" style="display: inline;">
                @csrf
                <button type="submit" class="quick-action-btn secondary">
                    <i class="fas fa-pause"></i>
                    Deactivate
                </button>
            </form>
            @else
            <form method="POST" action="{{ route('admin_toggle_customer_activation', $customer) }}" style="display: inline;">
                @csrf
                <button type="submit" class="quick-action-btn success">
                    <i class="fas fa-play"></i>
                    Activate
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="stats-overview">
        <div class="stat-overview-card">
            <div class="stat-overview-value">{{ $customer->total_orders }}</div>
            <div class="stat-overview-label">Total Orders</div>
            <div class="stat-overview-change positive">{{ $customer->completed_orders }} completed</div>
        </div>
        <div class="stat-overview-card">
            <div class="stat-overview-value">${{ number_format($customer->total_spent, 2) }}</div>
            <div class="stat-overview-label">Total Spent</div>
            @if($customer->total_savings > 0)
            <div class="stat-overview-change positive">${{ number_format($customer->total_savings, 2) }} saved</div>
            @endif
        </div>
        <div class="stat-overview-card">
            <div class="stat-overview-value">{{ number_format($analytics['financial']['avg_order_value'], 2) }}</div>
            <div class="stat-overview-label">Avg Order Value</div>
        </div>
        <div class="stat-overview-card">
            <div class="stat-overview-value">{{ $analytics['activity']['ratings_given'] }}</div>
            <div class="stat-overview-label">Reviews Given</div>
            @if($customer->average_rating > 0)
            <div class="stat-overview-change">{{ number_format($customer->average_rating, 1) }}/5 avg rating</div>
            @endif
        </div>
        <div class="stat-overview-card">
            <div class="stat-overview-value">{{ $analytics['activity']['messages_sent'] }}</div>
            <div class="stat-overview-label">Messages Sent</div>
            @if($customer->unread_messages > 0)
            <div class="stat-overview-change negative">{{ $customer->unread_messages }} unread</div>
            @endif
        </div>
        <div class="stat-overview-card">
            <div class="stat-overview-value">
                @if($customer->last_order_date)
                {{ $customer->last_order_date->diffForHumans() }}
                @else
                Never
                @endif
            </div>
            <div class="stat-overview-label">Last Order</div>
        </div>
    </div>

    <!-- Detailed Tabs -->
    <div class="detail-tabs">
        <div class="tab-nav">
            <button class="tab-nav-item active" data-tab="orders">
                <i class="fas fa-shopping-cart"></i>
                Orders ({{ $orders->total() }})
            </button>
            <button class="tab-nav-item" data-tab="reviews">
                <i class="fas fa-star"></i>
                Reviews ({{ $ratings->total() }})
            </button>
            <button class="tab-nav-item" data-tab="messages">
                <i class="fas fa-comments"></i>
                Messages ({{ $messages->total() }})
            </button>
            <button class="tab-nav-item" data-tab="transactions">
                <i class="fas fa-credit-card"></i>
                Transactions ({{ $transactions->total() }})
            </button>
            <button class="tab-nav-item" data-tab="notifications">
                <i class="fas fa-bell"></i>
                Notifications ({{ $notifications->total() }})
            </button>
            <button class="tab-nav-item" data-tab="activity">
                <i class="fas fa-history"></i>
                Admin Activity ({{ $activity_logs->total() }})
            </button>
            <button class="tab-nav-item" data-tab="sessions">
                <i class="fas fa-mobile-alt"></i>
                Login Sessions ({{ $sessions->total() }})
            </button>
        </div>

        <!-- Orders Tab -->
        <div class="tab-content active" id="orders">
            @if($orders->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Service</th>
                        <th>Provider</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>
                            <div style="font-weight: 600;">{{ $order->service->service ?? 'N/A' }}</div>
                            <div style="font-size: 0.875rem; color: #718096;">
                                {{ $order->service->category->name ?? 'N/A' }}
                            </div>
                        </td>
                        <td>
                            <div>{{ $order->service->user->full_name ?? 'N/A' }}</div>
                            <div style="font-size: 0.875rem; color: #718096;">
                                {{ $order->service->user->email ?? 'N/A' }}
                            </div>
                        </td>
                        <td>${{ number_format($order->price, 2) }}</td>
                        <td>
                            <span class="order-status {{ $order->status == 2 ? 'completed' : ($order->status == 1 ? 'pending' : 'cancelled') }}">
                                @if($order->status == 2) Completed
                                @elseif($order->status == 1) Pending
                                @elseif($order->status == 3) Cancelled
                                @else Unprocessed
                                @endif
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="#" onclick="alert('Order details coming soon!')" class="quick-action-btn secondary" style="padding: 0.375rem 0.75rem; font-size: 0.8rem; opacity: 0.6;">
                                <i class="fas fa-eye"></i>
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 1rem;">
                {{ $orders->links() }}
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <h3>No Orders Yet</h3>
                <p>This customer hasn't placed any orders.</p>
            </div>
            @endif
        </div>

        <!-- Reviews Tab -->
        <div class="tab-content" id="reviews">
            <div style="width: 100%; overflow-x: auto;">
                @if($ratings->count() > 0)
                <table class="data-table" style="width: 100%; min-width: 800px;">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Service</th>
                            <th style="width: 15%;">Rating</th>
                            <th style="width: 40%;">Review</th>
                            <th style="width: 15%;">Date</th>
                            <th style="width: 5%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ratings as $rating)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $rating->service->service ?? 'N/A' }}</div>
                                <div style="font-size: 0.875rem; color: #718096;">
                                    Provider: {{ $rating->service->user->full_name ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="color: #fbbf24;">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <=$rating->rate)
                                            <i class="fas fa-star"></i>
                                            @else
                                            <i class="far fa-star"></i>
                                            @endif
                                            @endfor
                                    </div>
                                    <span>{{ $rating->rate }}/5</span>
                                </div>
                            </td>
                            <td>
                                <div style="word-wrap: break-word; max-width: 300px;">
                                    {{ $rating->message }}
                                </div>
                            </td>
                            <td>{{ $rating->created_at->format('M d, Y') }}</td>
                            <td>
                                <button onclick="alert('Rating management coming soon!')" class="quick-action-btn danger" style="padding: 0.375rem 0.75rem; font-size: 0.8rem; opacity: 0.6;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="margin-top: 1rem; display: flex; justify-content: center;">
                    {{ $ratings->links() }}
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-star"></i>
                    <h3>No Reviews Yet</h3>
                    <p>This customer hasn't written any reviews.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Messages Tab -->
        <div class="tab-content" id="messages">
            <div style="width: 100%; overflow-x: auto;">
                @if($messages->count() > 0)
                <table class="data-table" style="width: 100%; min-width: 800px;">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Conversation With</th>
                            <th style="width: 40%;">Latest Message</th>
                            <th style="width: 15%;">Direction</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 10%;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $message)
                        <tr>
                            <td>
                                @php
                                $otherUserId = $message->from == $customer->id ? $message->to : $message->from;
                                $otherUser = \App\Models\User::find($otherUserId);
                                @endphp
                                <div style="font-weight: 600;">{{ $otherUser->full_name ?? 'Unknown User' }}</div>
                                <div style="font-size: 0.875rem; color: #718096;">{{ $otherUser->email ?? 'N/A' }}</div>
                            </td>
                            <td style="word-wrap: break-word;">
                                {{ $message->latest_message }}
                            </td>
                            <td>
                                @if($message->from == $customer->id)
                                <span style="color: #22c55e;"><i class="fas fa-arrow-right"></i> Sent</span>
                                @else
                                <span style="color: #667eea;"><i class="fas fa-arrow-left"></i> Received</span>
                                @endif
                            </td>
                            <td>
                                @if($message->from == $customer->id && !$message->seen_by_to)
                                <span style="color: #ef4444;">Unread</span>
                                @elseif($message->to == $customer->id && !$message->seen_by_from)
                                <span style="color: #ef4444;">Unread</span>
                                @else
                                <span style="color: #22c55e;">Read</span>
                                @endif
                            </td>
                            <td>{{ $message->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="margin-top: 1rem; display: flex; justify-content: center;">
                    {{ $messages->links() }}
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-comments"></i>
                    <h3>No Messages</h3>
                    <p>This customer hasn't sent or received any messages.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Transactions Tab -->
        <div class="tab-content" id="transactions">
            <div style="margin-bottom: 1rem;">
                <button class="quick-action-btn primary" onclick="openAddTransactionModal()">
                    <i class="fas fa-plus"></i>
                    Add Manual Transaction
                </button>
            </div>

            <div style="width: 100%; overflow-x: auto;">
                @if($transactions->count() > 0)
                <table class="data-table" style="width: 100%; min-width: 1000px;">
                    <thead>
                        <tr>
                            <th style="width: 12%;">Type</th>
                            <th style="width: 12%;">Amount</th>
                            <th style="width: 10%;">Order</th>
                            <th style="width: 15%;">Payment Method</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 31%;">Description</th>
                            <th style="width: 10%;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>
                                <span class="profile-badge {{ $transaction->type === 'credit' ? 'success' : ($transaction->type === 'refund' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                            <td style="font-weight: 600;">
                                @if($transaction->type === 'credit' || $transaction->type === 'refund')
                                +${{ number_format($transaction->amount, 2) }}
                                @else
                                -${{ number_format($transaction->amount, 2) }}
                                @endif
                            </td>
                            <td>
                                @if($transaction->order)
                                <span style="color: #667eea;">
                                    #{{ $transaction->order->id }}
                                </span>
                                @else
                                N/A
                                @endif
                            </td>
                            <td>{{ $transaction->payment_method ?? 'N/A' }}</td>
                            <td>
                                <span class="profile-badge {{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td style="word-wrap: break-word;">
                                {{ $transaction->description }}
                            </td>
                            <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="margin-top: 1rem; display: flex; justify-content: center;">
                    {{ $transactions->links() }}
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-credit-card"></i>
                    <h3>No Transactions</h3>
                    <p>No transaction history available for this customer.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Notifications Tab -->
        <div class="tab-content" id="notifications">
            <div style="width: 100%; overflow-x: auto;">
                @if($notifications->count() > 0)
                <table class="data-table" style="width: 100%; min-width: 800px;">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Message</th>
                            <th style="width: 15%;">Type</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 20%;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $notification)
                        <tr>
                            <td style="word-wrap: break-word;">{{ $notification->text }}</td>
                            <td>
                                <span class="profile-badge info">{{ ucfirst($notification->data_type) }}</span>
                            </td>
                            <td>
                                @if($notification->is_read)
                                <span style="color: #22c55e;"><i class="fas fa-check"></i> Read</span>
                                @elseif($notification->is_new)
                                <span style="color: #ef4444;"><i class="fas fa-exclamation"></i> New</span>
                                @else
                                <span style="color: #718096;"><i class="fas fa-eye"></i> Seen</span>
                                @endif
                            </td>
                            <td>{{ $notification->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="margin-top: 1rem; display: flex; justify-content: center;">
                    {{ $notifications->links() }}
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-bell"></i>
                    <h3>No Notifications</h3>
                    <p>No notifications sent to this customer.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Admin Activity Tab -->
        <div class="tab-content" id="activity">
            <div style="width: 100%; overflow-x: auto;">
                @if($activity_logs->count() > 0)
                <table class="data-table" style="width: 100%; min-width: 1000px;">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Admin</th>
                            <th style="width: 15%;">Action</th>
                            <th style="width: 25%;">Description</th>
                            <th style="width: 25%;">Changes</th>
                            <th style="width: 20%;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activity_logs as $log)
                        <tr>
                            <td>{{ $log->admin->name ?? 'Unknown Admin' }}</td>
                            <td>
                                <span class="profile-badge info">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                            </td>
                            <td style="word-wrap: break-word;">{{ $log->description }}</td>
                            <td>
                                @if($log->old_values || $log->new_values)
                                <details style="font-size: 0.8rem;">
                                    <summary style="cursor: pointer; color: #667eea;">View Changes</summary>
                                    <div style="margin-top: 0.5rem; padding: 0.5rem; background: #f8fafc; border-radius: 4px; word-wrap: break-word;">
                                        @if($log->old_values)
                                        <div><strong>Before:</strong> {{ json_encode($log->old_values) }}</div>
                                        @endif
                                        @if($log->new_values)
                                        <div><strong>After:</strong> {{ json_encode($log->new_values) }}</div>
                                        @endif
                                    </div>
                                </details>
                                @else
                                N/A
                                @endif
                            </td>
                            <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="margin-top: 1rem; display: flex; justify-content: center;">
                    {{ $activity_logs->links() }}
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <h3>No Admin Activity</h3>
                    <p>No admin actions recorded for this customer.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Login Sessions Tab -->
        <div class="tab-content" id="sessions">
            @if($sessions->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Device</th>
                        <th>Location</th>
                        <th>IP Address</th>
                        <th>Login Time</th>
                        <th>Logout Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $session)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $session->device_name ?? 'Unknown Device' }}</div>
                            <div style="font-size: 0.875rem; color: #718096;">{{ ucfirst($session->device_type ?? 'unknown') }}</div>
                        </td>
                        <td>{{ $session->location ?? 'Unknown' }}</td>
                        <td>{{ $session->ip_address ?? 'N/A' }}</td>
                        <td>{{ $session->login_at->format('M d, Y H:i') }}</td>
                        <td>
                            @if($session->logout_at)
                            {{ $session->logout_at->format('M d, Y H:i') }}
                            @else
                            N/A
                            @endif
                        </td>
                        <td>
                            <span class="profile-badge {{ $session->is_active ? 'success' : 'secondary' }}">
                                {{ $session->is_active ? 'Active' : 'Ended' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 1rem;">
                {{ $sessions->links() }}
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-mobile-alt"></i>
                <h3>No Login Sessions</h3>
                <p>No login session data available for this customer.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Transaction Modal -->
<div id="addTransactionModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; padding: 2rem; max-width: 500px; width: 90%;">
        <h3 style="margin: 0 0 1.5rem 0;">Add Manual Transaction</h3>
        <form method="POST" action="{{ route('admin_add_customer_transaction', $customer) }}">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Transaction Type</label>
                <select name="type" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <option value="">Select Type</option>
                    <option value="credit">Credit (Add Money)</option>
                    <option value="debit">Debit (Remove Money)</option>
                    <option value="refund">Refund</option>
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Amount</label>
                <input type="number" name="amount" step="0.01" min="0.01" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px;" />
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Payment Method</label>
                <input type="text" name="payment_method" placeholder="e.g., Manual Adjustment, Cash, etc." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px;" />
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Description</label>
                <textarea name="description" required rows="3" placeholder="Reason for this transaction..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; resize: vertical;"></textarea>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="closeAddTransactionModal()" class="quick-action-btn secondary">Cancel</button>
                <button type="submit" class="quick-action-btn primary">Add Transaction</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-nav-item');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');

                // Remove active class from all tabs and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                // Add active class to clicked tab and corresponding content
                this.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
            });
        });
    });

    function openAddTransactionModal() {
        document.getElementById('addTransactionModal').style.display = 'flex';
    }

    function closeAddTransactionModal() {
        document.getElementById('addTransactionModal').style.display = 'none';
    }

    // Close modal when clicking outside
    document.getElementById('addTransactionModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddTransactionModal();
        }
    });
</script>

@endsection