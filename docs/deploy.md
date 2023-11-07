# LaravelアプリケーションのEC2へのデプロイ

## はじめに

このドキュメントでは、Githubリポジトリ上のLaravelアプリケーションをGitHub ActionsとAWS CodeDeployを通じてEC2インスタンスへデプロイを行うプロセスについて説明します。

## 目的
現在のデプロイプロセスは、ビルド作業をEC2インスタンス上で実施しており、これが原因でデプロイ時にインスタンスが過剰な負荷に耐えられず停止する事態が発生しています。  
このプロセスを改善することで、効率的な開発フローを確立し、製品品質の向上を目指すとともに、リリース時間の短縮を実現します。

## 前提条件

- 設定済みの GitHub リポジトリが存在する。
- AWS アカウントがあり、AWS リソースが準備されている。
- 必要な AWS IAM ロールとポリシーが設定されている。
- 必要なシークレットが GitHub Secrets に設定されている。

## アーキテクチャ

簡単なシステムアーキテクチャ図とともに、CI/CDパイプラインの全体像を説明します。

## GitHub Actionsの設定
デプロイプロセスはGitHub Actionsを使用して自動化されており、具体的な設定はリポジトリ内のワークフローファイルに定義されています。以下のリンクからワークフローファイルを確認できます。

[Production EC2 Deploy Workflow File](/.github/workflows/prod_ec2_deploy_laravel.yaml)  
[Staging EC2 Deploy Workflow File](/.github/workflows/stg_ec2_deploy_laravel.yaml)

- シークレットの設定  
    デプロイに必要な環境変数やリソース名をセキュアに管理するため、以下のシークレットを GitHub リポジトリに設定します。
    | Name | Description |
    |:---|:---|
    |PROD_AWS_DEPLOY_APP_NAME|本番環境で作成したCodeDeployのデプロイアプリケーション名|
    |PROD_AWS_DEPLOY_GROUP_NAME|本番環境で作成したCodeDeployのデプロイグループ名|
    |PROD_AWS_DEPLOY_SOURCE_BUCKET_NAME|本番環境で作成したデプロイ用のソースコードを置くS3バケット名|
    |PROD_AWS_PARAMETER_STORE_ENV_FILE_NAME|本番環境で作成した.envの内容を設定したパラメータストア名|
    |PROD_AWS_IAM_ROLE_GITHUBACTIONS_ARN|本番環境で作成したGihubActions用IAMロールARN|
    |STG_AWS_DEPLOY_APP_NAME|開発環境で作成したCodeDeployのデプロイアプリケーション名|
    |STG_AWS_DEPLOY_GROUP_NAME|開発環境で作成したCodeDeployのデプロイグループ名|
    |STG_AWS_DEPLOY_SOURCE_BUCKET_NAME|開発環境で作成したデプロイ用のソースコードを置くS3バケット名|
    |STG_AWS_PARAMETER_STORE_ENV_FILE_NAME|開発環境で作成した.envの内容を設定したパラメータストア名|
    |STG_AWS_IAM_ROLE_GITHUBACTIONS_ARN|開発環境で作成したGihubActions用IAMロールARN|
    |SLACK_WEBHOOK_URL|デプロイ通知用SlackチャンネルWebHookURL|

- ビルド環境
  - 検証環境: プルリクエストによってトリガーされます。
  - 本番環境: workflow_dispatch イベントにより手動でトリガーされます。

- ビルド環境:  
    - 使用するランナー: `ubuntu-latest`

- アクションステップの概要
  1. ブランチのチェックアウト: GitHubリポジトリからコードをチェックアウトします。
  2. AWS認証: OIDCを介してAWSへの認証を行います。
  3. .envファイルの取得: AWS SSM Parameter Storeから環境設定ファイルを取得します。
  4. 依存関係のインストール: 必要な依存関係をインストールします。
  5. ソースのアップロード: アプリケーションのソースコードを S3 にアップロードします。
  6. CodeDeploy によるデプロイ: AWS CodeDeploy を使用してデプロイを実行します。
  7. デプロイの結果確認: デプロイの成功を確認します。
  8. Slack 通知: デプロイの結果を Slack に通知します。

- デプロイ時アクション  
    - .github/appspec.yaml
    - .github/scripts  
    ビルド時に上記をソースディレクトリにコピーし、EC2内でのデプロイ時に下記の順番でシェルスクリプトを実行します。
    また、シェル実行が必要ないものは空の内容で配置してください。

    1. scripts/ApplicationStop.sh  
        - 実行中のサービスを停止に使用します
    2. scripts/BeforeInstall.sh
        - ファイルの復号や現在のバージョンのバックアップの作成などの事前インストールに使用します
    3. scripts/AfterInstall.sh
        - アプリケーションの設定やファイルのアクセス許可の変更などに使用します
    4. scripts/ApplicationStart.sh
        - 実行中に停止したサービスを再起動に使用します
